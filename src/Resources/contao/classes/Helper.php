<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @package ${CARET}
 * @author  Martin Kunitzsch <m.kunitzsch@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Exporter;


use HeimrichHannot\Haste\Dca\General;
use HeimrichHannot\MultiColumnEditor\Backend\MultiColumnEditor;

class Helper
{
    public static function getArchiveName($strTable)
    {
        $strPTable = $GLOBALS['TL_DCA'][$strTable]['config']['ptable'];
        $intPid    = \Input::get('id');
        if ($strPTable)
        {
            $objInstance = General::getModelInstance($strPTable, $intPid);

            return $objInstance->title;
        }
        else
        {
            return $strTable;
        }
    }

    public static function getJoinTables($intExporter)
    {
        if (($objExporter = ExporterModel::findByPk($intExporter)) === null || !$objExporter->addJoinTables)
        {
            return [];
        }

        return MultiColumnEditor::fetchEach('joinTable', deserialize($objExporter->joinTables, true));
    }

    public static function getJoinTablesAndConditions($intExporter)
    {
        if (($objExporter = ExporterModel::findByPk($intExporter)) === null || !$objExporter->addJoinTables)
        {
            return [];
        }

        return deserialize($objExporter->joinTables, true);
    }
}