<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoExporterBundle\Event;


use Contao\File;
use HeimrichHannot\ContaoExporterBundle\Exporter\AbstractExporter;
use Symfony\Contracts\EventDispatcher\Event;

class ModifyMediaFile extends Event
{
    const NAME = 'huh.exporter.event.modifymediafile';
    /**
     * @var string
     */
    private $newFileName;
    /**
     * @var File
     */
    private $file;
    /**
     * @var string
     */
    private $field;
    /**
     * @var string
     */
    private $path;
    /**
     * @var AbstractExporter
     */
    private $exporter;

    public function __construct(string $newFileName, File $file, string $field, string $path, AbstractExporter $exporter)
    {
        $this->newFileName = $newFileName;
        $this->file = $file;
        $this->field = $field;
        $this->path = $path;
        $this->exporter = $exporter;
    }

    /**
     * @return string
     */
    public function getNewFileName(): string
    {
        return $this->newFileName;
    }

    /**
     * @param string $newFileName
     */
    public function setNewFileName(string $newFileName): void
    {
        $this->newFileName = $newFileName;
    }

    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }

    /**
     * @param File $file
     */
    public function setFile(File $file): void
    {
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @param string $field
     */
    public function setField(string $field): void
    {
        $this->field = $field;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return AbstractExporter
     */
    public function getExporter(): AbstractExporter
    {
        return $this->exporter;
    }

    /**
     * @param AbstractExporter $exporter
     */
    public function setExporter(AbstractExporter $exporter): void
    {
        $this->exporter = $exporter;
    }


}
