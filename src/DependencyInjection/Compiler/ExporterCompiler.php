<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoExporterBundle\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ExporterCompiler implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('huh.exporter.manager.exporter'))
        {
            return;
        }
        $definition = $container->findDefinition('huh.exporter.manager.exporter');

        $taggedServices = $container->findTaggedServiceIds('huh_exporter.exporter');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addExporter', array($id, new Reference($id)));
        }
    }
}