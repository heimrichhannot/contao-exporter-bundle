<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoExporterBundle\ContaoManager;


use HeimrichHannot\ContaoExporterBundle\Exporter\ExporterInterface;

class ExporterManager
{
    protected $exporterIds = [];
    protected $exporterFileTypes = [];

    /**
     * @return array
     */
    public function getExporterIds(): array
    {
        return $this->exporterIds;
    }

    /**
     * @return array
     */
    public function getExporterFileTypes(): array
    {
        return $this->exporterFileTypes;
    }

    /**
     * Add an exporter to the registry.
     *
     * @param string $serviceId
     * @param ExporterInterface $exporter
     */
    public function addExporter(string $serviceId, ExporterInterface $exporter) {
        $this->exporterIds[$exporter->getName()] = $serviceId;
        foreach ($exporter->getSupportedFileTypes() as $fileType)
        {
            $this->exporterFileTypes[$fileType][] = $serviceId;
        }
    }

    public function getExporterByFileType(string $fileType)
    {
        return $this->exporterFileTypes[$fileType] ?: [];
    }
}