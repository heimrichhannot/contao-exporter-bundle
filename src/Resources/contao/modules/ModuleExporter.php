<?php

namespace HeimrichHannot\Exporter;

class ModuleExporter
{

    public static function exportBe($objDc)
    {
        $strGlobalOperationKey = \Input::get('key');
        $strTable              = \Input::get('table') ?: $objDc->table;

        if (!$strGlobalOperationKey || !$strTable)
        {
            return;
        }

        if (($objConfig = ExporterModel::findByKeyAndTable($strGlobalOperationKey, $strTable)) === null)
        {
            if (empty($_SESSION['TL_ERROR']))
            {
                \Message::addError($GLOBALS['TL_LANG']['MSC']['exporter']['noConfigFound']);
                \Controller::redirect($_SERVER['HTTP_REFERER']);
            }
        }
        else
        {
            static::export($objConfig, \Input::get('id'));
        }
    }

    /**
     * @param       $objConfig
     * @param null  $objEntity
     * @param array $arrFields
     *
     * @return bool|object The exporter or false if no exporter had been found (or error happened).
     */
    public static function export($objConfig, $objEntity = null, array $arrFields = [])
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

    public static function getGlobalOperation($strName, $strLabel = '', $strIcon = '')
    {
        $arrOperation = [
            'label'      => &$strLabel,
            'href'       => 'key=' . $strName,
            'class'      => 'header_' . $strName . '_entities',
            'icon'       => $strIcon,
            'attributes' => 'onclick="Backend.getScrollOffset()"',
        ];

        return $arrOperation;
    }

    public static function getOperation($strName, $strLabel = '', $strIcon = '')
    {
        $arrOperation = [
            'label' => &$strLabel,
            'href'  => 'key=' . $strName,
            'icon'  => $strIcon,
        ];

        return $arrOperation;
    }

    public static function getBackendModule()
    {
        return ['HeimrichHannot\Exporter\ModuleExporter', 'exportBe'];
    }

}
