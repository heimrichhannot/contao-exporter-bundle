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

use Contao\Folder;
use Contao\System;
use HeimrichHannot\ContaoExporterBundle\Event\ModifyFieldValueEvent;
use HeimrichHannot\UtilsBundle\Driver\DC_Table_Utils;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\MimeType\FileinfoMimeTypeGuesser;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

abstract class AbstractPhpSpreadsheetExporter extends AbstractTableExporter
{
    protected $arrExportFields = [];

    /**
     * @param null $entity
     * @param array $fields
     * @return mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    protected function doExport($entity = null, array $fields = [])
    {
        return $this->exportList($this->getEntities($entity));
    }

    /**
     * @param $databaseResult
     * @return Spreadsheet
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function exportList($databaseResult)
    {
        Settings::setCache(new FilesystemCache('huh.exporter.phpspreadsheet'));
        $table = $this->config->linkedTable;
        $arrDca         = &$GLOBALS['TL_DCA'][$table];
        $spreadsheet    = new Spreadsheet();

        $columnIndex = 1;
        $rowIndex    = 1;

        // header
        if ($this->config->addHeaderToExportTable && is_array($this->headerFields)) {
            foreach ($this->headerFields as $value) {
                $spreadsheet->setActiveSheetIndex(0)->setCellValueByColumnAndRow($columnIndex, $rowIndex, $value);
                $this->processHeaderRow($columnIndex);
                $columnIndex++;
            }
            $rowIndex++;
        }

        $colCount = 0;
        // body
        if ($databaseResult->numRows > 0) {

            while ($databaseResult->next()) {
                $row         = $databaseResult->row();
                $columnIndex = 1;

                $dcTable               = new DC_Table_Utils($table);
                $dcTable->activeRecord = $databaseResult;
                $strId                 = $table . '.id';
                $dcTable->id           = $databaseResult->{$strId};

                // trigger onload_callback since these could modify the dca
                if (is_array($arrDca['config']['onload_callback'])) {
                    foreach ($arrDca['config']['onload_callback'] as $callback) {
                        if (is_array($callback)) {
                            if (!isset($arrOnload[implode(',', $callback)])) {
                                $arrOnload[implode(',', $callback)] = 0;
                            }
                            System::importStatic($callback[0])->{$callback[1]}($dcTable);
                        } elseif (is_callable($callback)) {
                            $callback($dcTable);
                        }
                    }
                }

                foreach ($row as $key => $value)
                {
                    $strField = str_replace($table . '.', '', $key);
                    $value = $this->config->localizeFields ? $this->container->get('huh.utils.form')->prepareSpecialValueForOutput(
                        $strField, $value, $dcTable
                    ) : $value;

                    if (is_array($value)) {
                        $value = $this->container->get('huh.utils.array')->flattenArray($value);
                    }

                    $event = $this->dispatcher->dispatch(ModifyFieldValueEvent::NAME, new ModifyFieldValueEvent($value, $strField, $row, $columnIndex, $rowIndex, $this));

                    $spreadsheet->setActiveSheetIndex(0)->setCellValueByColumnAndRow(
                        $event->getColumnIndex(),
                        $event->getRowIndex(),
                        html_entity_decode($event->getValue())
                    );

                    $this->processBodyRow($columnIndex);

                    $columnIndex++;
                }
//                $spreadsheet->getActiveSheet()->getRowDimension($rowIndex)->setRowHeight(-1);
                $colCount = max($colCount, $columnIndex);
                $rowIndex++;
            }
        }

        $spreadsheet->setActiveSheetIndex(0);
        for ($i = 1; $i <= $colCount; $i++)
        {
            $spreadsheet->getActiveSheet()->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }
        $spreadsheet->getActiveSheet()->setTitle('Export');

        return $spreadsheet;
    }

    /**
     * @param Spreadsheet $spreadsheet
     * @param string $fileDir
     * @param string $fileName
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportToDownload($spreadsheet, string $fileDir, string $fileName)
    {
        // send file to browser
        $writer = $this->getDocumentWriter($spreadsheet);
        $this->createHeaders($fileName);
        $writer->save('php://output');
        exit();
    }

    /**
     * @param Spreadsheet $spreadsheet
     * @param string $fileDir
     * @param string $fileName
     * @return bool
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportToFile($spreadsheet, string $fileDir, string $fileName)
    {
        $writer = $this->getDocumentWriter($spreadsheet);
        try {
            $writer->save($fileDir . '/' . $fileName);
        } catch (\Exception $e) {
            return false;
        }
        return true;

    }


    /**
     * @param Spreadsheet $spreadsheet
     * @return \PhpOffice\PhpSpreadsheet\Writer\IWriter
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    protected function getDocumentWriter(Spreadsheet $spreadsheet) {
        return IOFactory::createWriter($spreadsheet, ucfirst($this->config->fileType));
    }

    protected function createHeaders($fileName)
    {
        header("Content-Type: text/plain");
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
    }

    public function processHeaderRow(int $col)
    {
    }

    public function processBodyRow(int $col)
    {
    }

    /**
     * Update the responce
     *
     * @param BinaryFileResponse $response
     */
    protected function beforeResponce(BinaryFileResponse &$response)
    {
    }


}
