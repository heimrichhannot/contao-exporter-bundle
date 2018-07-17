<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoExporterBundle\Exporter\Concrete;


use HeimrichHannot\ContaoExporterBundle\Exporter\AbstractPhpSpreadsheetExporter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\BaseWriter;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class CsvExporter extends AbstractPhpSpreadsheetExporter
{

    public static function getName(): string
    {
        return 'csv-exporter';
    }

    public static function getSupportedFileTypes(): array
    {
        return ['csv'];
    }

    public static function getSupportedExportTypes(): array
    {
        // TODO: Implement getSupportedExportTypes() method.
    }

    protected function getDocumentWriter(Spreadsheet $spreadsheet): BaseWriter
    {
        $writer = new Csv($spreadsheet);
        $writer->setDelimiter($this->config->fieldDelimiter ?: ',')->setEnclosure($this->config->fieldEnclosure ?: '"')->setSheetIndex(0);
        return $writer;

    }

    protected function createHeaders($fileName)
    {
        parent::createHeaders($fileName);
        header("Content-Type: application/csv");
    }


    public static function getSupportedExportTarget(): array
    {
        // TODO: Implement getSupportedExportTarget() method.
    }
}