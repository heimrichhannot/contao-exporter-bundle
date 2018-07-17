<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 *
 * @author  Oliver Janke <o.janke@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\ContaoExporterBundle\Model;

use Contao\Input;
use Contao\Model;

/**
 * Class ExporterModel
 * @package HeimrichHannot\ContaoExporterBundle\Model
 *
 * @property int $id
 * @property int $tstamp
 * @property string $title
 * @property string $type
 * @property string $linkedTable
 * @property string $globalOperationKey
 * @property string $restrictToPids
 * @property string $skipFields
 * @property string $skipLabels
 * @property string $addUnformattedFields
 * @property string $tableFieldsForExport
 * @property string $fileType
 * @property string $exporterClass
 * @property string $fieldDelimiter
 * @property string $fieldEnclosure
 * @property string $addHeaderToExportTable
 * @property string $overrideHeaderFieldLabels
 * @property string $headerFieldLabels
 * @property string $compressionType
 * @property string $localizeHeader
 * @property string $localizeFields
 * @property string $target
 * @property string $fileDir
 * @property string $useHomeDir
 * @property string $fileSubDirName
 * @property string $fileName
 * @property string $fileNameAddDatime
 * @property string $fileNameAddDatimeFormat
 * @property string $addJoinTables
 * @property string $joinTables
 * @property string $whereClause
 * @property string $orderBy
 * @property string $pdfBackground
 * @property string $pdfTemplate
 * @property string $pdfCss
 * @property string $pdfFonts
 * @property string $pdfMargins
 * @property string $pdfTitle
 * @property string $pdfSubject
 * @property string $pdfCreator
 * @property string $useProtectedHomeDir
 */
class ExporterModel extends Model
{

    protected static $strTable = 'tl_exporter';

    public static function findByKeyAndTable($key, $table, array $options = [])
    {
        $t = static::$strTable;

        $columns[] = "($t.globalOperationKey='" . $key . "')";
        $columns[] = "($t.linkedTable='" . $table . "')";

        if (TL_MODE == 'BE' && ($pid = Input::get('id')) && !Input::get('act')) {
            $columns[]     = "($t.restrictToPids REGEXP '\"$pid\"' OR $t.restrictToPids IS NULL OR $t.restrictToPids = '' OR $t.restrictToPids = 'a:0:{}')";
            $options['order'] = "$t.restrictToPids DESC";
        }

        return static::findOneBy($columns, null, $options);
    }

}
