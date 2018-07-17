<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoExporterBundle;


use HeimrichHannot\ContaoExporterBundle\DependencyInjection\Compiler\ExporterCompiler;
use HeimrichHannot\ContaoExporterBundle\DependencyInjection\ExporterExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HeimrichHannotContaoExporterBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new ExporterExtension();
    }

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ExporterCompiler());
    }


}