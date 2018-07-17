<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoExporterBundle\Exporter;


use HeimrichHannot\ContaoExporterBundle\Model\ExporterModel;

interface ExporterInterface
{
    public static function getName(): string;
    public static function getSupportedFileTypes(): array;
    public static function getSupportedExportTypes(): array;
    public static function getSupportedExportTarget(): array;

    /**
     * Export with given parameters.
     *
     * @param ExporterModel|null $config
     * @param null $entity
     * @param array $fields
     * @return bool
     */
    public function export(ExporterModel $config = null, $entity = null, array $fields = []): bool;
}