<?php
/**
 * Created by PhpStorm.
 * User: tkoerner
 * Date: 17.07.18
 * Time: 14:37
 */

namespace HeimrichHannot\ContaoExporterBundle\Exporter;


interface ExportTargetFileInterface
{
    /**
     * Start the download
     *
     * @param mixed $data
     * @param string $fileDir
     * @param string $fileName
     * @return mixed
     */
    public function exportToFile($data, string $fileDir, string $fileName);
}