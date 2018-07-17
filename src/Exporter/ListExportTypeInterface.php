<?php
/**
 * Created by PhpStorm.
 * User: tkoerner
 * Date: 16.07.18
 * Time: 16:30
 */

namespace HeimrichHannot\ContaoExporterBundle\Exporter;



interface ListExportTypeInterface
{
    const TYPE = 'list';

    public function getListData();
}