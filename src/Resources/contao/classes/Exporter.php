<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 *
 * @author  Oliver Janke <o.janke@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */
namespace HeimrichHannot\Exporter;

use HeimrichHannot\Haste\Util\Arrays;
use HeimrichHannot\Haste\Util\Files;
use HeimrichHannot\Haste\Util\FormSubmission;

abstract class Exporter extends \Controller
{
    protected $objConfig;

    /**
     * @var \PHPExcel
     */
    protected $objPhpExcel;
    protected $strWriterOutputType;
    protected $strFilename;
    protected $strFileDir;
    protected $strTemplate = '';

    const TYPE_ITEM = 'item';
    const TYPE_LIST = 'list';

    const TARGET_DOWNLOAD = 'download';
    const TARGET_FILE     = 'file';

    public function __construct($objConfig)
    {
        $this->objConfig = $objConfig;
        $arrSkipFields   = ['id', 'tstamp', 'title'];

        // add all config attributes to the scope of this class
        foreach ($objConfig->row() as $strField => $varValue)
        {
            if (in_array($strField, $arrSkipFields))
            {
                continue;
            }

            $this->{$strField} = $varValue;
        }

        \Controller::loadDataContainer($this->linkedTable);
        \System::loadLanguageFile($this->linkedTable);
    }

    public function export($entity = null, array $fields = [])
    {
        if (!$this->strFilename)
        {
            $this->strFilename = $this->buildFilename($entity);
        }

        if (!$this->strFileDir && $this->target == static::TARGET_FILE)
        {
            $this->strFileDir = $this->buildFileDir($entity);
        }

        $objResult = $this->doExport($entity, $fields);

        switch ($this->target)
        {
            case static::TARGET_FILE:
                $this->exportToFile($objResult);
                break;
            case static::TARGET_DOWNLOAD:
                $this->exportToDownload($objResult);
                break;
        }
    }

    protected function buildFileDir($objEntity = null)
    {
        if ($this->fileDir && $objFolder = \FilesModel::findByUuid($this->fileDir))
        {
            $objMember = \FrontendUser::getInstance();
            $strDir    = $objFolder->path;

            if ($this->useHomeDir && FE_USER_LOGGED_IN && $objMember->assignDir && $objMember->homeDir)
            {
                $strDir = Files::getPathFromUuid($objMember->homeDir);
            }

            if (in_array('protected_homedirs', \ModuleLoader::getActive()))
            {
                if ($this->useProtectedHomeDir && $objMember->assignProtectedDir && $objMember->protectedHomeDir)
                {
                    $strDir = Files::getPathFromUuid($objMember->protectedHomeDir);
                }
            }

            if ($this->fileSubDirName)
            {
                $strDir .= '/' . $this->fileSubDirName;
            }

            if (isset($GLOBALS['TL_HOOKS']['exporter_modifyFileDir']) && is_array($GLOBALS['TL_HOOKS']['exporter_modifyFileDir']))
            {
                foreach ($GLOBALS['TL_HOOKS']['exporter_modifyFileDir'] as $callback)
                {
                    $objCallback = \System::importStatic($callback[0]);
                    $strFixedDir = $objCallback->{$callback[1]}($strDir, $this);

                    $strDir = $strFixedDir ?: $strDir;
                }
            }

            return $strDir;
        }

        throw new \Exception('No exporter fileDir defined!');
    }

    protected function buildFilename($objEntity = null)
    {
        $strFilename = $this->fileName ?: 'export';

        if ($this->fileNameAddDatime)
        {
            $strFilename = date($this->fileNameAddDatimeFormat ?: 'Y-m-d') . '_' . $strFilename;
        }

        if (isset($GLOBALS['TL_HOOKS']['exporter_modifyFilename']) && is_array($GLOBALS['TL_HOOKS']['exporter_modifyFilename']))
        {
            foreach ($GLOBALS['TL_HOOKS']['exporter_modifyFilename'] as $callback)
            {
                $objCallback      = \System::importStatic($callback[0]);
                $strFixedFilename = $objCallback->{$callback[1]}($strFilename, $this);

                $strFilename = $strFixedFilename ?: $strFilename;
            }
        }

        return $strFilename . '.' . $this->fileType;
    }

    protected function setHeaderFields()
    {
        if (!$this->addHeaderToExportTable)
        {
            return;
        }

        $arrFields = [];

        foreach (deserialize($this->tableFieldsForExport, true) as $strField)
        {
            list($strTable, $strField) = explode('.', $strField);

            $blnRawField     = strpos($strField, EXPORTER_RAW_FIELD_SUFFIX) !== false;
            $strRawFieldName = str_replace(EXPORTER_RAW_FIELD_SUFFIX, '', $strField);
            \Controller::loadDataContainer($strTable);
            \System::loadLanguageFile($strTable);

            $strFieldLabel = $GLOBALS['TL_DCA'][$strTable]['fields'][$blnRawField ? $strRawFieldName : $strField]['label'][0];
            $strLabel     = $strField;

            if ($this->overrideHeaderFieldLabels
                && ($arrRow = Arrays::getRowInMcwArray('field', $strTable . '.' . $strField, deserialize($this->headerFieldLabels, true))) !== false
            )
            {
                $strLabel = $arrRow['label'];
            }
            elseif ($this->localizeHeader && $strFieldLabel)
            {
                $strLabel = $strFieldLabel;
            }

            $arrFields[] = strip_tags(html_entity_decode($strLabel)) . ($blnRawField ? $GLOBALS['TL_LANG']['MSC']['exporter']['unformatted'] : '');
        }

        if (isset($GLOBALS['TL_HOOKS']['exporter_modifyHeaderFields'])
            && is_array(
                $GLOBALS['TL_HOOKS']['exporter_modifyXlsHeaderFields']
            )
        )
        {
            foreach ($GLOBALS['TL_HOOKS']['exporter_modifyHeaderFields'] as $callback)
            {
                $objCallback = \System::importStatic($callback[0]);
                $arrFields   = $objCallback->{$callback[1]}($arrFields, $this);
            }
        }

        $this->arrHeaderFields = $arrFields;
    }

    protected function getEntities()
    {
        $arrExportFields = [];
        $arrDca          = $GLOBALS['TL_DCA'][$this->linkedTable];

        foreach (deserialize($this->tableFieldsForExport, true) as $strField)
        {
            if (strpos($strField, EXPORTER_RAW_FIELD_SUFFIX) !== false)
            {
                $arrExportFields[] = str_replace(EXPORTER_RAW_FIELD_SUFFIX, '', $strField) . ' AS "' . $strField . '"';
            }
            else
            {
                $arrExportFields[] = $strField . ' AS "' . $strField . '"';
            }
        }

        // SELECT
        $strQuery = 'SELECT ' . implode(',', $arrExportFields) . ' FROM ' . $this->linkedTable;

        // JOIN
        if ($this->addJoinTables)
        {
            $arrJoinTables = Helper::getJoinTablesAndConditions($this->objConfig->id);

            foreach ($arrJoinTables as $joinT)
            {
                $strQuery .= ' INNER JOIN ' . $joinT['joinTable'] . ' ON ' . $joinT['joinCondition'];
            }
        }

        // WHERE
        $arrWheres = [];
        if ($this->whereClause)
        {
            $arrWheres[] = html_entity_decode($this->whereClause);
        }

        // limit to archive
        if (TL_MODE == 'BE' && ($this->type == Exporter::TYPE_LIST || !$this->type))
        {
            $strAct = \Input::get('act');
            $intPid = \Input::get('id');

            if ($intPid && !$strAct && is_array($arrDca['fields']) && $arrDca['config']['ptable'])
            {
                $arrWheres[] = 'pid = ' . $intPid;
            }
        }

        if (!empty($arrWheres))
        {
            $strQuery .= ' WHERE ' . implode(
                    ' AND ',
                    array_map(
                        function ($val)
                        {
                            return '(' . $val . ')';
                        },
                        $arrWheres
                    )
                );
        }

        // ORDER BY
        if ($this->orderBy)
        {
            $strQuery .= ' ORDER BY ' . $this->orderBy;
        }

        return \Database::getInstance()->prepare($strQuery)->execute();
    }

    protected abstract function doExport($objEntity = null, array $arrFields = []);

    public abstract function exportToDownload($objResult);

    public abstract function exportToFile($objResult);

    public function processHeaderRow($intCol)
    {
    }

    public function processBodyRow($intCol)
    {
    }

    public function updateWriter($objWriter)
    {
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->strFilename;
    }

    /**
     * @param mixed $strFilename
     */
    public function setFilename($strFilename)
    {
        $this->strFilename = $strFilename;
    }

    /**
     * @return mixed
     */
    public function getFileDir()
    {
        return $this->strFileDir;
    }

    /**
     * @param mixed $strFileDir
     */
    public function setFileDir($strFileDir)
    {
        $this->strFileDir = $strFileDir;
    }
}