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


use Box\Spout\Common\Type;
use Box\Spout\Writer\CSV\Writer;
use Box\Spout\Writer\WriterFactory;
use HeimrichHannot\ContaoExporterBundle\Exporter\AbstractPhpSpreadsheetExporter;

class CsvExporter extends AbstractPhpSpreadsheetExporter
{
    public function getSupportedFileTypes(): array
    {
        return ['csv'];
    }

    protected function getDocumentWriter()
    {
        /** @var Writer $writer */
        $writer = WriterFactory::create(Type::CSV);

        $writer->setFieldDelimiter($this->config->fieldDelimiter ?: ',');
        $writer->setFieldEnclosure($this->config->fieldEnclosure ?: '"');

        return $writer;
    }
}