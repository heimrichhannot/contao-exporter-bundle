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


use Contao\Model;

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

    /**
     * Return the entity to export
     *
     * @param Model|int|string $id Id, alias, or instance of the item
     * @return Model
     */
    public function getEntity($id): Model;

    /**
     * Prepare field values for output, if fielddata not set from external
     *
     * @param array $fields
     * @param Model $entity
     * @return array
     */
    public function prepareItemFields(Model $entity, array $fields = []): array;
}