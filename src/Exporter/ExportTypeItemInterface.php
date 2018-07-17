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


interface ExportTypeItemInterface
{
    /**
     * Export the item
     *
     * @param $entity
     * @param array $fields
     * @return mixed
     */
    public function exportItem($entity, array $fields = []);
}