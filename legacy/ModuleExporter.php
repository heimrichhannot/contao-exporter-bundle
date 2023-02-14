<?php

/*
 * Copyright (c) 2023 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\Exporter;

use Contao\System;

/**
 * Class ModuleExporter.
 *
 * @deprecated This class is just kept for compatibility reasons and will soon be removed. Please see UPGRADE.md
 */
class ModuleExporter
{
    /**
     * @param $objDc
     *
     * @throws \Exception
     *
     * @deprecated This function is just kept for compatibility reasons and will soon be removed. Please see UPGRADE.md
     */
    public static function exportBe($objDc)
    {
        System::getContainer()->get('huh.exporter.action.backendexport')->export($objDc);
    }

    /**
     * @param      $objConfig
     * @param null $objEntity
     *
     * @throws \Exception
     *
     * @return bool|object the exporter or false if no exporter had been found (or error happened)
     *
     * @deprecated This function is just kept for compatibility reasons and will soon be removed. Please see UPGRADE.md
     */
    public static function export($objConfig, $objEntity = null, array $arrFields = [])
    {
        $result = System::getContainer()->get('huh.exporter.action.export')->export($objConfig, $objEntity, $arrFields);

        return $result->exporter;
    }

    /**
     * @param $strName
     * @param string $strLabel
     * @param string $strIcon
     *
     * @return array
     *
     * @deprecated This function is just kept for compatibility reasons and will soon be removed. Please see UPGRADE.md
     */
    public static function getGlobalOperation($strName, $strLabel = '', $strIcon = '')
    {
        return System::getContainer()->get('huh.exporter.action.backendexport')->getGlobalOperation($strName, $strLabel, $strIcon);
    }

    /**
     * @param $strName
     * @param string $strLabel
     * @param string $strIcon
     *
     * @return array
     *
     * @deprecated This function is just kept for compatibility reasons and will soon be removed. Please see UPGRADE.md
     */
    public static function getOperation($strName, $strLabel = '', $strIcon = '')
    {
        $arrOperation = [
            'label' => &$strLabel,
            'href' => 'key='.$strName,
            'icon' => $strIcon,
        ];

        return $arrOperation;
    }

    /**
     * @return array
     *
     * @deprecated This function is just kept for compatibility reasons and will soon be removed. Please see UPGRADE.md
     */
    public static function getBackendModule()
    {
        return ['huh.exporter.action.backendexport', 'export'];
    }
}
