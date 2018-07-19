<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoExporterBundle\Action;


use HeimrichHannot\ContaoExporterBundle\Manager\ExporterManager;
use HeimrichHannot\ContaoExporterBundle\Model\ExporterModel;

class ExportAction
{
    /**
     * @var ExporterManager
     */
    private $exporterManager;

    public function __construct(ExporterManager $exporterManager)
    {
        $this->exporterManager = $exporterManager;
    }

    /**
     * @param ExporterModel $config
     * @param mixed $entity
     * @param array $arrFields
     *
     * @return bool|object The exporter or false if no exporter had been found (or error happened).
     * @throws \Exception
     */
    public function export(ExporterModel $config, $entity = null, array $arrFields = [])
    {

        if (!$config->exporterClass)
        {
            throw new \Exception('Missing exporter class for exporter config ID ' . $config->id);
        }
        $exporter = $this->exporterManager->getExporterByClassName($config->exporterClass);
        if (!$exporter) {
            throw new \Exception('Exporter class for exporter configuration '.$config->id.' not found');
        }
        return $exporter->export($config, $entity, $arrFields);
    }
}
