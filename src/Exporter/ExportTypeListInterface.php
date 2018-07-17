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
     * @return mixed
     */
    public function exportList();
}