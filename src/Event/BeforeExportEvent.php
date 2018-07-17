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


use Contao\Model;
use HeimrichHannot\ContaoExporterBundle\Exporter\AbstractExporter;
use HeimrichHannot\ContaoExporterBundle\Model\ExporterModel;
use Symfony\Component\EventDispatcher\Event;

class BeforeExportEvent extends Event
{
    const NAME = 'huh.exporter.event.before_export';
    /**
     * @var Model
     */
    private $entity;
    /**
     * @var array
     */
    private $fields;
    /**
     * @var string
     */
    private $fileDir;
    /**
     * @var string
     */
    private $fileName;
    /**
     * @var ExporterModel
     */
    private $config;
    /**
     * @var AbstractExporter
     */
    private $exporter;

    public function __construct(Model $entity, array $fields, string $fileDir, string $fileName, ExporterModel $config, AbstractExporter $exporter)
    {
        $this->entity = $entity;
        $this->fields = $fields;
        $this->fileDir = $fileDir;
        $this->fileName = $fileName;
        $this->config = $config;
        $this->exporter = $exporter;
    }

    /**
     * @return ExporterModel
     */
    public function getConfig(): ExporterModel
    {
        return $this->config;
    }

    /**
     * @param ExporterModel $config
     */
    public function setConfig(ExporterModel $config): void
    {
        $this->config = $config;
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

    /**
     * @return Model
     */
    public function getEntity(): Model
    {
        return $this->entity;
    }

    /**
     * @param Model $entity
     */
    public function setEntity(Model $entity): void
    {
        $this->entity = $entity;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * @return string
     */
    public function getFileDir(): string
    {
        return $this->fileDir;
    }

    /**
     * @param string $fileDir
     */
    public function setFileDir(string $fileDir): void
    {
        $this->fileDir = $fileDir;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }
}