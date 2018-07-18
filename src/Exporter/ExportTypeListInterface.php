<?php
/**
 * Created by PhpStorm.
 * User: tkoerner
 * Date: 16.07.18
 * Time: 16:30
 */

namespace HeimrichHannot\ContaoExporterBundle\Exporter;



interface ExportTypeListInterface
{
    /**
     * Export list
     *
     * @param $entity
     * @return mixed
     */
    public function exportList($entity);

    /**
     * Retrieve and return list entries
     *
     * @param $entity
     * @return mixed
     */
    public function getEntities($entity);
}
