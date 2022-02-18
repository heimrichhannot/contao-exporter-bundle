<?php

/*
 * Copyright (c) 2022 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\ContaoExporterBundle\Migration;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Migration\MigrationInterface;
use Contao\CoreBundle\Migration\MigrationResult;
use HeimrichHannot\ContaoExporterBundle\Exporter\AbstractExporter;
use HeimrichHannot\ContaoExporterBundle\Exporter\Concrete\CsvExporter;
use HeimrichHannot\ContaoExporterBundle\Exporter\Concrete\ExcelExporter;
use HeimrichHannot\ContaoExporterBundle\Exporter\Concrete\MediaExporter;
use HeimrichHannot\ContaoExporterBundle\Exporter\Concrete\PdfExporter;
use HeimrichHannot\ContaoExporterBundle\Model\ExporterModel;

class ModuleMigration implements MigrationInterface
{
    private ContaoFramework $contaoFramework;

    public function __construct(ContaoFramework $contaoFramework)
    {
        $this->contaoFramework = $contaoFramework;
    }

    public function getName(): string
    {
        return 'Exporter Module Migration';
    }

    public function shouldRun(): bool
    {
        $this->contaoFramework->initialize();

        foreach (array_keys(static::legacyClassMapping()) as $value) {
            if (ExporterModel::findByExporterClass($value)) {
                return true;
            }
        }

        if (ExporterModel::findByType('formhybrid')) {
            return true;
        }

        if (ExporterModel::findByFileType('xls')) {
            return true;
        }

        return false;
    }

    public function run(): MigrationResult
    {
        $this->contaoFramework->initialize();

        foreach (static::legacyClassMapping() as $legacyClass => $newClass) {
            if ($exporter = ExporterModel::findByExporterClass($legacyClass)) {
                $exporter->exporterClass = $newClass;
                ++$updatedClasses;
                $exporter->save();
            }
        }

        if ($exporters = ExporterModel::findByType('formhybrid')) {
            foreach ($exporters as $exporter) {
                $exporter->type = AbstractExporter::TYPE_ITEM;
                $exporter->save();
            }
        }

        if ($exporters = ExporterModel::findByFileType('xls')) {
            foreach ($exporters as $exporter) {
                $exporter->fileType = 'xlsx';
                $exporter->save();
            }
        }

        return new MigrationResult(true, 'Exporter Module Migration executed successfully');
    }

    public static function legacyClassMapping(): array
    {
        return [
            'HeimrichHannot\Exporter\Concrete\XlsExporter' => ExcelExporter::getAlias(),
            'HeimrichHannot\Exporter\Concrete\CsvExporter' => CsvExporter::getAlias(),
            'HeimrichHannot\Exporter\Concrete\MediaExporter' => MediaExporter::getAlias(),
            'HeimrichHannot\Exporter\Concrete\PdfExporter' => PdfExporter::getAlias(),
        ];
    }
}
