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


use Contao\Controller;
use Contao\Input;
use Contao\Message;
use HeimrichHannot\ContaoExporterBundle\Model\ExporterModel;

class ModuleManager
{
    /**
     * @param       $objConfig
     * @param null $objEntity
     * @param array $arrFields
     *
     * @return bool|object The exporter or false if no exporter had been found (or error happened).
     * @throws \Exception
     */
    public function export($objConfig, $objEntity = null, array $arrFields = [])
    {
        $objExporter = null;

        if (!$objConfig->exporterClass)
        {
            throw new \Exception('Missing exporter class for exporter config ID ' . $objConfig->id);
        }

        $objExporter = new $objConfig->exporterClass($objConfig);

        if ($objExporter)
        {
            $objExporter->export($objEntity, $arrFields);

            return $objExporter;
        }

        return false;
    }

    public function getOperation($strName, $strLabel = '', $strIcon = '')
    {
        $arrOperation = [
            'label' => &$strLabel,
            'href'  => 'key=' . $strName,
            'icon'  => $strIcon,
        ];

        return $arrOperation;
    }


}