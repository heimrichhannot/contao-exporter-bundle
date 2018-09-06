<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 *
 * @author  Oliver Janke <o.janke@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\ContaoExporterBundle\Exporter\Concrete;

use HeimrichHannot\ContaoExporterBundle\Exporter\AbstractPhpSpreadsheetExporter;

class ExcelExporter extends AbstractPhpSpreadsheetExporter
{
    /**
     * Return a list of supported file types
     *
     * @return array
     */
    public function getSupportedFileTypes(): array
    {
        return ['xlsx'];
    }
}