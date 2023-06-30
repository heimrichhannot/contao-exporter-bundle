<?php

/*
 * Copyright (c) 2023 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\ContaoExporterBundle\Exporter;

use Contao\Controller;
use Contao\StringUtil;
use Contao\System;
use HeimrichHannot\ContaoExporterBundle\Event\ModifyHeaderFieldsEvent;

abstract class AbstractTableExporter extends AbstractExporter implements ExportTypeListInterface
{
    protected $headerFields;

    protected function beforeExport($fileDir, $fileName)
    {
        parent::beforeExport($fileDir, $fileName);
        $this->setHeaderFields();
    }

    protected function setHeaderFields()
    {
        if (!$this->config->addHeaderToExportTable) {
            return;
        }

        $headerFields = [];

        foreach (StringUtil::deserialize($this->config->tableFieldsForExport, true) as $strField) {
            list($strTable, $strField) = explode('.', $strField);

            $blnRawField = false !== strpos($strField, EXPORTER_RAW_FIELD_SUFFIX);
            $strRawFieldName = str_replace(EXPORTER_RAW_FIELD_SUFFIX, '', $strField);
            Controller::loadDataContainer($strTable);
            System::loadLanguageFile($strTable);

            $strFieldLabel = ($GLOBALS['TL_DCA'][$strTable]['fields'][$blnRawField ? $strRawFieldName : $strField]['label'][0] ?? $strField);
            $strLabel = $strField;

            if ($this->config->overrideHeaderFieldLabels
                && ($arrRow = $this->container->get('huh.utils.array')->getArrayRowByFieldValue(
                    'field',
                    $strTable.'.'.$strField,
                    StringUtil::deserialize($this->config->headerFieldLabels, true))
                ) !== false
            ) {
                $strLabel = $arrRow['label'];
            } elseif ($this->config->localizeHeader && $strFieldLabel) {
                $strLabel = $strFieldLabel;
            }

            $headerFields[] = strip_tags(html_entity_decode($strLabel)).($blnRawField ? $GLOBALS['TL_LANG']['MSC']['exporter']['unformatted'] : '');
        }

        $event = $this->dispatcher->dispatch(new ModifyHeaderFieldsEvent($headerFields, $this), ModifyHeaderFieldsEvent::NAME);

        $this->headerFields = $event->getHeaderFields();
    }
}
