<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoExporterBundle\EventListener\DataContainer;


use Contao\MemberGroupModel;
use HeimrichHannot\ContaoExporterBundle\Model\ExporterModel;

class Module
{
    public static function getExportConfigs()
    {
        $arrOptions = [];
        $arrConfigs = ExporterModel::findAll();

        if ($arrConfigs !== null)
        {
            foreach ($arrConfigs as $objConfig)
            {
                $arrOptions[$objConfig->id] = $objConfig->title;
            }
        }

        return $arrOptions;
    }

    public static function getMemberGroups()
    {
        $arrOptions      = [];
        $arrMemberGroups = MemberGroupModel::findAllActive();

        if ($arrMemberGroups !== null)
        {
            foreach ($arrMemberGroups as $objMemberGroup)
            {
                $arrOptions[$objMemberGroup->id] = $objMemberGroup->name;
            }
        }

        return $arrOptions;
    }
}