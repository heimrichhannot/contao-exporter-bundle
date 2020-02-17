<?php

namespace HeimrichHannot\Exporter;

use Contao\System;
use HeimrichHannot\ContaoExporterBundle\Action\BackendExportAction;

/**
 * Class ModuleExporter
 * @package HeimrichHannot\Exporter
 *
 * @deprecated This class is just kept for compatibility reasons and will soon be removed. Please see UPGRADE.md
 */
class ModuleExporter
{

    /**
     * @param $objDc
     * @throws \Exception
     *
     * @deprecated This function is just kept for compatibility reasons and will soon be removed. Please see UPGRADE.md
     */
    public static function exportBe($objDc)
    {
        System::getContainer()->get('huh.exporter.action.backendexport')->export($objDc);
    }

    /**
     * @param       $objConfig
     * @param null $objEntity
     * @param array $arrFields
     *
     * @return bool|object The exporter or false if no exporter had been found (or error happened).
     * @throws \Exception
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
     * @return array
     *
     * @deprecated This function is just kept for compatibility reasons and will soon be removed. Please see UPGRADE.md
     */
    public static function getGlobalOperation($strName, $strLabel = '', $strIcon = '')
    {
        return BackendExportAction::getGlobalOperation($strName, $strLabel, $strIcon);
    }

    /**
     * @param $strName
     * @param string $strLabel
     * @param string $strIcon
     * @return array
     *
     * @deprecated This function is just kept for compatibility reasons and will soon be removed. Please see UPGRADE.md
     */
    public static function getOperation($strName, $strLabel = '', $strIcon = '')
    {
        $arrOperation = [
            'label' => &$strLabel,
            'href'  => 'key=' . $strName,
            'icon'  => $strIcon,
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
