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


use HeimrichHannot\ContaoExporterBundle\Exception\ExporterConfigurationException;
use HeimrichHannot\ContaoExporterBundle\Exception\ExportNotPossibleException;
use HeimrichHannot\ContaoExporterBundle\Exception\ExportTypeNotSupportedException;
use HeimrichHannot\ContaoExporterBundle\Model\ExporterModel;

interface ExporterInterface
{
    /**
     * Return a list of supported file types
     *
     * Example: ['csv','xslt']
     *
     * @return array
     */
    public function getSupportedFileTypes(): array;

    /**
     * Export with given parameters.
     *
     * @param ExporterModel|null $config
     * @param null $entity
     * @param array $fields
     * @return bool
     *
     * @throws ExportNotPossibleException
     * @throws ExportTypeNotSupportedException
     * @throws ExporterConfigurationException
     */
    public function export(ExporterModel $config = null, $entity = null, array $fields = []);
}