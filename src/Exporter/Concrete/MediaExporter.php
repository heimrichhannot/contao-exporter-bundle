<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 *
 * @author  Oliver Janke <o.janke@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\ContaoExporterBundle\Exporter\Concrete;

use Contao\Database\Result;
use Contao\Environment;
use Contao\File;
use Contao\Folder;
use Contao\ZipWriter;
use HeimrichHannot\ContaoExporterBundle\Event\ModifyMediaFile;
use HeimrichHannot\ContaoExporterBundle\Exporter\AbstractExporter;
use HeimrichHannot\ContaoExporterBundle\Exporter\ExportTypeListInterface;
use HeimrichHannot\UtilsBundle\Driver\DC_Table_Utils;

class MediaExporter extends AbstractExporter implements ExportTypeListInterface
{
    /**
     * @var string
     */
    protected $tempPath = 'var/tmp/huh_exporter/';

    protected function doExport($objEntity = null, array $arrFields = [])
    {
        return $this->exportList($this->getEntities($objEntity));
    }

    protected function buildFileName($entity, string $fileType = '')
    {
        $fileType = empty($fileType) ? $this->config->compressionType : $fileType;
        $fileName = parent::buildFileName($entity, $fileType);
        return $fileName;
    }

    /**
     * Return a list of supported file types
     *
     * Example: ['csv','xslt']
     *
     * @return array
     */
    public function getSupportedFileTypes(): array
    {
        return ['media'];
    }

    /**
     * Export list
     *
     * @param Result $result
     * @return mixed
     */
    public function exportList($result)
    {
        if (!$result->numRows > 0)
        {
            return null;
        }

        $mediaFileList = [];

        while ($result->next())
        {
            $row = $result->row();
            foreach ($row as $key => $value)
            {
                $dataTable               = new DC_Table_Utils($this->config->linkedTable);
                $dataTable->activeRecord = $result;
                $strId                   = $this->linkedTable . '.id';
                $dataTable->id           = $result->{$strId};

                $field = str_replace($this->linkedTable . '.', '', $key);

                $value = $this->container->get('huh.utils.form')->prepareSpecialValueForOutput($field, $value, $dataTable);

                if (strpos($value, ', ') !== false)
                {
                    $value = explode(', ', $value);
                }

                if (!is_array($value))
                {
                    $value = [$value];
                }

                foreach ($value as $path)
                {
                    if ($path && ($file = new File(str_replace(Environment::get('url'), '', $path), true)) !== null && $file->exists())
                    {
                        $event = $this->dispatcher->dispatch(
                            new ModifyMediaFile('', $file, $field, $path, $this),
                            ModifyMediaFile::NAME
                        );

                        if (!empty($event->getNewFileName()))
                        {
                            $mediaFileList[] = ['path' => $event->getFile()->path, 'name' => $event->getNewFileName()];
                        }
                        else {
                            $mediaFileList[] = $event->getFile()->path;
                        }


                    }
                }
            }
        }

        return $mediaFileList;
    }

    public function createArchive(array $mediaFileList, string $fileDir, string $fileName)
    {
        $tempPath  = $this->getUniqueTempFolderPath('mediaexporter') . $fileName;
        $zipWriter = new ZipWriter($tempPath);

        foreach ($mediaFileList as $mediaFilePath)
        {
            if (is_array($mediaFilePath))
            {
                $zipWriter->addFile($mediaFilePath['path'], $mediaFilePath['name']);
            }
            else {
                $zipWriter->addFile($mediaFilePath);
            }
        }
        $zipWriter->close();

        return new File($tempPath);
    }

    /**
     * @param array $result
     * @param string $fileDir
     * @param string $fileName
     */
    public function exportToDownload($result, string $fileDir, string $fileName)
    {
        $file = $this->createArchive($result, $fileDir, $fileName);
        $file->sendToBrowser($fileName, 'D');
        $this->clearTempFolder($file->dirname);
    }

    /**
     * @param array $mediaFileList
     * @param string $fileDir
     * @param string $fileName
     * @return bool
     * @throws \Exception
     */
    public function exportToFile($mediaFileList, string $fileDir, string $fileName)
    {
        $file    = $this->createArchive($mediaFileList, $fileDir, $fileName);
        $tmpPath = $file->dirname;
        $file->write($fileDir, $fileName);
        $file->close();
        return $this->clearTempFolder($tmpPath);
    }

    /**
     * @param $tmpPath
     * @return bool
     */
    protected function clearTempFolder($tmpPath): bool
    {
        try
        {
            $folder = new Folder($tmpPath);
            $folder->delete();
        } catch (\Exception $exception)
        {
        }
        return true;
    }
}
