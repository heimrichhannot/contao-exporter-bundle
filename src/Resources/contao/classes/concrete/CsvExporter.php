<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 *
 * @author  Oliver Janke <o.janke@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Exporter\Concrete;

use Contao\DC_Table;
use HeimrichHannot\Exporter\PhpExcelExporter;
use HeimrichHannot\Haste\Dca\General;
use HeimrichHannot\Haste\Util\Files;

class CsvExporter extends PhpExcelExporter
{
    protected $strFileType         = EXPORTER_FILE_TYPE_CSV;
    protected $strWriterOutputType = 'CSV';

    public function processHeaderRow($intCol)
    {
        $this->objPhpExcel->getActiveSheet()->getStyle(\PHPExcel_Cell::stringFromColumnIndex($intCol))->getAlignment()->setWrapText(true);
    }

    public function processBodyRow($intCol)
    {
        $this->objPhpExcel->getActiveSheet()->getStyle(\PHPExcel_Cell::stringFromColumnIndex($intCol))->getAlignment()->setWrapText(true);
    }

    public function updateWriter($objWriter)
    {
        $objWriter->setDelimiter($this->fieldDelimiter ?: ',')->setEnclosure($this->fieldEnclosure ?: '"')->setSheetIndex(0);
    }
}