<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 *
 * @author  Oliver Janke <o.janke@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\ContaoExporterBundle\Exporter;

use HeimrichHannot\Haste\Dca\DC_HastePlus;
use HeimrichHannot\Haste\Util\Arrays;
use HeimrichHannot\Haste\Util\FormSubmission;

abstract class AbstractPhpSpreadsheetExporter extends AbstractTableExporter
{
    protected $arrExportFields = [];

    public static function getSupportedExportTypes(): array
    {
        return [AbstractExporter::TYPE_LIST];
    }


    protected function doExport($entity = null, array $fields = [])
    {
        $databaseResult = $this->getEntities();
        $arrDca      = $GLOBALS['TL_DCA'][$this->config->linkedTable];

        $intCol = 0;
        $intRow = 1;

        // header
        if ($this->config->addHeaderToExportTable && is_array($this->headerFields)) {
            foreach ($this->headerFields as $varValue) {
                $this->objPhpExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($intCol, $intRow, $varValue);
                $this->processHeaderRow($intCol);
                $intCol++;
            }
            $intRow++;
        }

        // body
        if ($databaseResult->numRows > 0) {

            while ($databaseResult->next()) {
                $arrRow = $databaseResult->row();
                $intCol = 0;

                $objDc               = new DC_HastePlus($this->linkedTable);
                $objDc->activeRecord = $databaseResult;
                $strId               = $this->linkedTable . '.id';
                $objDc->id           = $databaseResult->{$strId};

                // trigger onload_callback since these could modify the dca
                if (is_array($arrDca['config']['onload_callback'])) {
                    foreach ($arrDca['config']['onload_callback'] as $callback) {
                        if (is_array($callback)) {
                            if (!isset($arrOnload[implode(',', $callback)])) {
                                $arrOnload[implode(',', $callback)] = 0;
                            }

                            $this->import($callback[0]);
                            $this->{$callback[0]}->{$callback[1]}($objDc);
                        } elseif (is_callable($callback)) {
                            $callback($objDc);
                        }
                    }

                    // refresh
                    $arrDca = $GLOBALS['TL_DCA'][$this->linkedTable];
                }

                foreach ($arrRow as $key => $varValue) {
                    $strField = str_replace($this->linkedTable . '.', '', $key);

                    $varValue = $this->localizeFields ? FormSubmission::prepareSpecialValueForPrint(
                        $varValue,
                        $arrDca['fields'][$strField],
                        $this->linkedTable,
                        $objDc
                    ) : $varValue;

                    if (is_array($varValue)) {
                        $varValue = Arrays::flattenArray($varValue);
                    }

                    if (isset($GLOBALS['TL_HOOKS']['exporter_modifyFieldValue'])
                        && is_array(
                            $GLOBALS['TL_HOOKS']['exporter_modifyFieldValue']
                        )
                    ) {
                        foreach ($GLOBALS['TL_HOOKS']['exporter_modifyFieldValue'] as $callback) {
                            $objCallback = \System::importStatic($callback[0]);
                            $varValue    = $objCallback->{$callback[1]}($varValue, $strField, $arrRow, $intCol);
                        }
                    }

                    $this->objPhpExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(
                        $intCol,
                        $intRow,
                        html_entity_decode($varValue)
                    );

                    $this->objPhpExcel->getActiveSheet()->getColumnDimension(\PHPExcel_Cell::stringFromColumnIndex($intCol))->setAutoSize(true);
                    $this->processBodyRow($intCol);

                    $intCol++;
                }
                $this->objPhpExcel->getActiveSheet()->getRowDimension($intRow)->setRowHeight(-1);
                $intRow++;
            }
        }

        $this->objPhpExcel->setActiveSheetIndex(0);
        $this->objPhpExcel->getActiveSheet()->setTitle('Export');

        return $this->objPhpExcel;
    }

    public function exportToDownload($objResult)
    {
        $strTmpFile = 'system/tmp/' . $this->strFilename;

        // send file to browser
        $objWriter = \PHPExcel_IOFactory::createWriter($objResult, $this->strWriterOutputType);
        $this->updateWriter($objWriter);
        $objWriter->save(TL_ROOT . '/' . $strTmpFile);
        $objFile = new \File($strTmpFile);
        $objFile->sendToBrowser();
    }

    public function exportToFile($objResult)
    {

    }


}
