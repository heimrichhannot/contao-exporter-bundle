<?php

/*
 * Copyright (c) 2022 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\ContaoExporterBundle\ExporterConfiguration;

use Contao\Model;
use HeimrichHannot\ContaoExporterBundle\Exporter\AbstractExporter;
use HeimrichHannot\ContaoExporterBundle\Model\ExporterModel;

class ExporterConfiguration
{
    private string $table = '';
    private string $type = AbstractExporter::TYPE_LIST;
    private string $target = AbstractExporter::TARGET_DOWNLOAD;
    private string $fileFormat = '';
    private string $exportClass = '';
    private array $fields = [];
    private string $whereCondition = '';
    private string $orderByCondition = '';
    /** @var ExporterModel|null */
    private ?Model $model = null;

    public static function create(string $table, string $fileFormat, string $exporterClass): self
    {
        return (new self())
            ->setTable($table)
            ->setFileFormat($fileFormat)
            ->setExportClass($exporterClass)
            ;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function setTable(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    public function getFileFormat(): string
    {
        return $this->fileFormat;
    }

    public function setFileFormat(string $fileFormat): self
    {
        $this->fileFormat = $fileFormat;

        return $this;
    }

    public function getExportClass(): string
    {
        return $this->exportClass;
    }

    public function setExportClass(string $exportClass): self
    {
        $this->exportClass = $exportClass;

        return $this;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    public function getWhereCondition(): string
    {
        return $this->whereCondition;
    }

    public function setWhereCondition(string $whereCondition): self
    {
        $this->whereCondition = $whereCondition;

        return $this;
    }

    public function getOrderByCondition(): string
    {
        return $this->orderByCondition;
    }

    public function setOrderByCondition(string $orderByCondition): self
    {
        $this->orderByCondition = $orderByCondition;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function setTarget(string $target): self
    {
        $this->target = $target;

        return $this;
    }
}
