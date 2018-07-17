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

use HeimrichHannot\Exporter\PhpExcelExporter;

class XlsExporter extends PhpExcelExporter
{
	protected $strFileType = EXPORTER_FILE_TYPE_XLS;
	protected $strWriterOutputType = 'Excel5';
}