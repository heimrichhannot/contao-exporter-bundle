<?php

namespace HeimrichHannot\Exporter;


use HeimrichHannot\Haste\Dca\General;
use HeimrichHannot\Haste\Util\Classes;

class Backend extends \Controller
{
    public static function getTableFields($objDc)
    {
        if (($objExporter = ExporterModel::findByPk($objDc->id)) === null)
            return [];


        $blnUnformatted = $objExporter->addUnformattedFields && $objExporter->type != Exporter::TYPE_ITEM &&
                          $objExporter->fileType != EXPORTER_FILE_TYPE_MEDIA;
        $blnJoins       = $objExporter->addJoinTables;

        $arrOptions = static::doGetTableFields($objExporter->linkedTable, $blnUnformatted, $blnJoins);

        if ($objExporter->addJoinTables)
        {
            foreach (Helper::getJoinTables($objExporter->id) as $strTable)
            {
                $arrOptions = array_merge($arrOptions, static::doGetTableFields($strTable, $blnUnformatted, $blnJoins));
            }
        }

        return $arrOptions;
    }

    public static function doGetTableFields($strTable, $blnIncludeUnformatted = false, $blnPrefixTableName = false)
    {
        $arrOptions        = [];

        if (!$strTable)
        {
            return [];
        }

        \Controller::loadDataContainer($strTable);

        $arrFields = $GLOBALS['TL_DCA'][$strTable]['fields'];

        if (!is_array($arrFields) || empty($arrFields))
        {
            return $arrOptions;
        }

        foreach ($arrFields as $strField => $arrData)
        {
            $arrOptions[$strTable . '.' . $strField] = ($blnPrefixTableName ? $strTable . '.' : '') . $strField;
        }

        if ($blnIncludeUnformatted)
        {
            $arrOptionsRawKeys = array_map(
                function ($val)
                {
                    return $val . EXPORTER_RAW_FIELD_SUFFIX;
                },
                array_keys($arrOptions)
            );

            $arrOptionsRawValues = array_map(
                function ($val)
                {
                    return $val . $GLOBALS['TL_LANG']['MSC']['exporter']['unformatted'];
                },
                array_values($arrOptions)
            );

            $arrOptions += array_combine($arrOptionsRawKeys, $arrOptionsRawValues);
        }

        return $arrOptions;
    }

    /**
     * Searches through all backend modules to find the linked tables for the selected global operation key
     *
     * @param \DataContainer $dc
     *
     * @return array
     */
    public static function getLinkedTablesAsOptions(\DataContainer $objDc)
    {
        $arrTables             = [];
        $strGlobalOperationKey = $objDc->activeRecord->globalOperationKey;

        switch ($objDc->activeRecord->type)
        {
            case Exporter::TYPE_LIST:
                if ($strGlobalOperationKey)
                {
                    foreach ($GLOBALS['BE_MOD'] as $arrSection)
                    {
                        foreach ($arrSection as $strModule => $arrModule)
                        {
                            foreach ($arrModule as $strKey => $varValue)
                            {
                                if ($strKey === $strGlobalOperationKey)
                                {
                                    $arrTables[$strModule] = $arrModule['tables'];
                                }
                            }
                        }
                    }
                }
                break;
            default:
                $arrTables = General::getDataContainers();
        }

        return $arrTables;
    }

    /**
     * Get all tables for possible join
     *
     * @return array
     */
    public static function getAllTablesAsOptions()
    {
        return \Database::getInstance()->listTables();
    }

    /**
     * Searches through all backend modules to find global operation keys and returns a filtered list
     *
     * @return array
     */
    public static function getGlobalOperationKeysAsOptions()
    {
        $arrGlobalOperations = [];
        $arrSkipKeys         = ['callback', 'generate', 'icon', 'import', 'javascript', 'stylesheet', 'table', 'tables'];

        foreach ($GLOBALS['BE_MOD'] as $arrSection)
        {
            foreach ($arrSection as $arrModule)
            {
                foreach ($arrModule as $strKey => $varValue)
                {
                    if (!in_array($strKey, $arrGlobalOperations) && !in_array($strKey, $arrSkipKeys))
                    {
                        $arrGlobalOperations[] = $strKey;
                    }
                }
            }
        }
        sort($arrGlobalOperations);

        return $arrGlobalOperations;
    }

    /**
     * Return available PDF templates for the pdf exporter
     *
     * @return mixed
     */
    public function getPdfExporterTemplates()
    {
        return $this->getTemplateGroup('exporter_pdf_');
    }

    public static function getConfigsAsOptions($strType = null)
    {
        $arrOptions = [];
        if ($strType)
        {
            $objConfigs = ExporterModel::findByType($strType);
        }
        else
        {
            $objConfigs = ExporterModel::findAll();
        }

        if ($objConfigs !== null)
        {
            \Controller::loadDataContainer('tl_exporter');
            \System::loadLanguageFile('tl_exporter');

            while ($objConfigs->next())
            {
                $strExportType   = $GLOBALS['TL_LANG']['tl_exporter']['reference'][$objConfigs->type];
                $strExportTarget = $GLOBALS['TL_LANG']['tl_exporter']['reference'][$objConfigs->target];

                $arrOptions[$objConfigs->id] =
                    $objConfigs->title . ' (ID ' . $objConfigs->id . ($strType ? '' : ', Typ: ' . $strExportType) . ', Ziel: ' . $strExportTarget . ')';
            }
        }

        asort($arrOptions);

        return $arrOptions;
    }

    public static function getExporterClasses()
    {
        return Classes::getClassesInNamespace('HeimrichHannot\Exporter\Concrete');
    }

    public static function getTableArchives(\DataContainer $objDc)
    {
        $arrOptions = [];

        if ($objDc->activeRecord->linkedTable && $GLOBALS['TL_DCA'][$objDc->activeRecord->linkedTable]['config']['ptable'])
        {
            $objArchives = General::getTableArchives($objDc->activeRecord->linkedTable, [
                'order' => 'title ASC'
            ]
            );

            if ($objArchives !== null)
            {
                while ($objArchives->next())
                {
                    $arrOptions[$objArchives->id] = $objArchives->title;
                }
            }
        }

        return $arrOptions;
    }
}