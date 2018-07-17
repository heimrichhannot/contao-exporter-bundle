<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 *
 * @author  Oliver Janke <o.janke@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */
namespace HeimrichHannot\Exporter\Concrete;

use Contao\DC_Table;
use Contao\ZipWriter;
use HeimrichHannot\Exporter\Exporter;
use HeimrichHannot\Haste\Dca\DC_HastePlus;
use HeimrichHannot\Haste\Util\Arrays;
use HeimrichHannot\Haste\Util\Files;
use HeimrichHannot\Haste\Util\FormSubmission;

class MediaExporter extends Exporter
{
    protected function doExport($objEntity = null, array $arrFields = [])
    {
        switch ($this->type)
        {
            case Exporter::TYPE_LIST:
                $objDbResult  = $this->getEntities();
                $arrDca       = $GLOBALS['TL_DCA'][$this->linkedTable];
                $strTmpFile   = 'system/tmp/' . $this->strFilename;
                $strTmpFolder = str_replace('.' . $this->compressionType, '', $strTmpFile);

                if (!$objDbResult->numRows > 0)
                {
                    return null;
                }

                switch ($this->compressionType)
                {
                    default:
                        $objZip = new ZipWriter($strTmpFile);
                        break;
                }

                // write files
                while ($objDbResult->next())
                {
                    $arrRow = $objDbResult->row();
                    foreach ($arrRow as $key => $varValue)
                    {
                        $objDc               = new DC_HastePlus($this->linkedTable);
                        $objDc->activeRecord = $objDbResult;
                        $strId               = $this->linkedTable . '.id';
                        $objDc->id           = $objDbResult->{$strId};

                        $strField = str_replace($this->linkedTable . '.', '', $key);

                        $varValue = FormSubmission::prepareSpecialValueForPrint($varValue, $arrDca['fields'][$strField], $this->linkedTable, $objDc);

                        if (strpos($varValue, ', ') !== false)
                        {
                            $varValue = explode(', ', $varValue);
                        }

                        if (!is_array($varValue))
                        {
                            $varValue = [$varValue];
                        }

                        foreach ($varValue as $strPath)
                        {
                            if ($strPath && ($objFile = new \File(str_replace(\Environment::get('url'), '', $strPath), true)) !== null && $objFile->exists())
                            {
                                if (isset($GLOBALS['TL_HOOKS']['exporter_modifyMediaFilename'])
                                    && is_array(
                                        $GLOBALS['TL_HOOKS']['exporter_modifyMediaFilename']
                                    )
                                )
                                {
                                    foreach ($GLOBALS['TL_HOOKS']['exporter_modifyMediaFilename'] as $callback)
                                    {
                                        $objCallback      = \System::importStatic($callback[0]);
                                        $strFixedFilename = $objCallback->{$callback[1]}($objFile, $strField, $strPath, $this);

                                        if ($strFixedFilename)
                                        {
                                            $strTmpFixedFilename = $strTmpFolder . '/' . ltrim($strFixedFilename, '/');
                                            $objFile->copyTo($strTmpFixedFilename);
                                            $objFile->path = $strTmpFixedFilename;
                                        }
                                    }
                                }

                                switch ($this->compressionType)
                                {
                                    default:
                                        $objZip->addFile($objFile->path);
                                        break;
                                }
                            }
                        }
                    }
                }

                switch ($this->compressionType)
                {
                    default:
                        $objZip->close();
                        break;
                }

                $objTmpFolder = new \Folder($strTmpFolder);

                if (is_dir(TL_ROOT . '/' . $objTmpFolder->path))
                {
                    $objTmpFolder->delete();
                }

                $objFile = new \File($strTmpFile);

                return $objFile;

                break;
            case Exporter::TYPE_ITEM:
                break;
        }
    }

    public function exportToDownload($objResult)
    {
        $objResult->sendToBrowser($this->strFilename, 'D');
    }

    public function exportToFile($objResult)
    {
        if ($this->strFileDir && $this->strFilename)
        {
            $objResult->saveToFile($this->strFileDir, $this->strFilename);
        }
        else
        {
            throw new \Exception('No valid path for exporter!');
        }
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

        return $strFilename . '.' . $this->compressionType;
    }
}
