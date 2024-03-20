<?php

/*
 * Copyright (c) 2022 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\ContaoExporterBundle\DependencyInjection;

use HeimrichHannot\ContaoExporterBundle\ExportOperation\ExportOperationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ExporterExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container, new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yml');

        $container->registerForAutoconfiguration(ExportOperationInterface::class)
            ->addTag('huh.exporter.operation');
    }
}
