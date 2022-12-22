<?php

/*
 * Copyright (c) 2022 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\ContaoExporterBundle\Event;

use Contao\Model;
use HeimrichHannot\ContaoExporterBundle\Exporter\AbstractExporter;
use HeimrichHannot\ContaoExporterBundle\Model\ExporterModel;
use Symfony\Contracts\EventDispatcher\Event;

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
    private $fileName;
    /**
     * @var ExporterModel
     */
    private $config;
    /**
     * @var AbstractExporter
     */
    private $exporter;
    /**
     * @var string
     */
    private $fileDir;

    public function __construct($entity, array $fields, string $fileName, AbstractExporter $exporter, string $fileDir = '')
    {
        $this->entity = $entity;
        $this->fields = $fields;
        $this->fileName = $fileName;
        $this->exporter = $exporter;
        $this->fileDir = $fileDir;
    }

    public function getExporter(): AbstractExporter
    {
        return $this->exporter;
    }

    public function setExporter(AbstractExporter $exporter): void
    {
        $this->exporter = $exporter;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param mixed $entity
     */
    public function setEntity($entity): void
    {
        $this->entity = $entity;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }

    public function getFileDir(): string
    {
        return $this->fileDir;
    }

    public function setFileDir(string $fileDir): void
    {
        $this->fileDir = $fileDir;
    }
}
