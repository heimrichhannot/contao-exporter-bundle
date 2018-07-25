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


use Contao\Input;
use HeimrichHannot\ContaoExporterBundle\Exporter\AbstractTableExporter;
use HeimrichHannot\ContaoExporterBundle\Model\ExporterModel;
use Psr\Container\ContainerInterface;

class FrontendExportAction
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    /**
     * @param ExporterModel $config
     * @throws \Exception
     */
    public function export(ExporterModel $config)
    {
        $entity = $this->getEntity($config);
        $this->container->get('huh.exporter.action.export')->export($config, $entity);
    }

    public function getEntity(ExporterModel $config)
    {
        if (AbstractTableExporter::TYPE_LIST === $config->type)
        {
            return null;
        }
        switch ($config->entitySelector)
        {
            case 'auto_item':
                return Input::get('auto_item');
            case 'urlParameter':
                if (!$config->entityUrlParameter)
                {
                    return null;
                }
                return Input::get($config->entityUrlParameter);
            case 'static':
                return $config->entityStaticValue;
        }
        return null;
    }
}