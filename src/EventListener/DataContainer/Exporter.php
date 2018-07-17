<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoExporterBundle\EventListener\DataContainer;


use Contao\BackendUser;
use HeimrichHannot\ContaoExporterBundle\ContaoManager\ExporterManager;

class Exporter
{
    /**
     * @var ExporterManager
     */
    private $exporterManager;

    public function __construct(ExporterManager $exporterManager)
    {
        $this->exporterManager = $exporterManager;
    }


    public function checkPermission()
    {
        if (BackendUser::getInstance()->isAdmin)
        {
            return;
        }
    }

    public function getExporterClasses()
    {
        $list = [];
        $exporterList = $this->exporterManager->getAllExporter();
        foreach ($exporterList as $item)
        {
            $list = $item::getName();
        }

        return $list;
    }
}