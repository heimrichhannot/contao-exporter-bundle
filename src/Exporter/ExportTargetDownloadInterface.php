<?php
/**
 * Created by PhpStorm.
 * User: tkoerner
 * Date: 17.07.18
 * Time: 14:37
 */

namespace HeimrichHannot\ContaoExporterBundle\Exporter;


interface ExportTargetDownloadInterface
{
    /**
     * Start the download
     *
     * @param $data
     * @param string $fileDir
     * @param string $fileName
     * @return mixed
     */
    public function exportToDownload($data, string $fileDir, string $fileName);
}