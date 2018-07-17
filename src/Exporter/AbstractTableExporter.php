<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoExporterBundle\Exporter;


use Contao\Controller;
use Contao\System;
use HeimrichHannot\ContaoExporterBundle\Model\ExporterModel;

abstract class AbstractTableExporter extends AbstractExporter implements ExportTypeListInterface
{
    protected $headerFields;

    public function export(ExporterModel $config = null, $entity = null, array $fields = []): bool
    {
        $this->setHeaderFields();
        return parent::export($config, $entity, $fields);
    }


    protected function setHeaderFields()
    {
        if (!$this->config->addHeaderToExportTable)
        {
            return;
        }

        $headerFields = [];

        foreach (deserialize($this->config->tableFieldsForExport, true) as $strField)
        {
            list($strTable, $strField) = explode('.', $strField);

            $blnRawField     = strpos($strField, EXPORTER_RAW_FIELD_SUFFIX) !== false;
            $strRawFieldName = str_replace(EXPORTER_RAW_FIELD_SUFFIX, '', $strField);
            Controller::loadDataContainer($strTable);
            System::loadLanguageFile($strTable);

            $strFieldLabel = $GLOBALS['TL_DCA'][$strTable]['fields'][$blnRawField ? $strRawFieldName : $strField]['label'][0];
            $strLabel      = $strField;

            if ($this->config->overrideHeaderFieldLabels
                && ($arrRow =$this->container->get('huh.utils.array')->getArrayRowByFieldValue(
                    'field',
                    $strTable . '.' . $strField,
                    deserialize($this->config->headerFieldLabels, true))
                ) !== false
            )
            {
                $strLabel = $arrRow['label'];
            } elseif ($this->config->localizeHeader && $strFieldLabel)
            {
                $strLabel = $strFieldLabel;
            }

            $headerFields[] = strip_tags(html_entity_decode($strLabel)) . ($blnRawField ? $GLOBALS['TL_LANG']['MSC']['exporter']['unformatted'] : '');
        }

        //@TODO: replace
//        if (isset($GLOBALS['TL_HOOKS']['exporter_modifyHeaderFields'])
//            && is_array(
//                $GLOBALS['TL_HOOKS']['exporter_modifyXlsHeaderFields']
//            )
//        )
//        {
//            foreach ($GLOBALS['TL_HOOKS']['exporter_modifyHeaderFields'] as $callback)
//            {
//                $objCallback = \System::importStatic($callback[0]);
//                $headerFields   = $objCallback->{$callback[1]}($headerFields, $this);
//            }
//        }

        $this->headerFields = $headerFields;
    }
}