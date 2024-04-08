<?php

/*
 * Copyright (c) 2022 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\ContaoExporterBundle\ExportOperation;

class ExportOperationConfiguraton
{
    private bool $globalOperation = true;
    private string $table = '';
    private string $icon = '';
    private array $types = [];

    public static function create(string $table, array $types, bool $globalOperation = true): self
    {
        return (new self())->setTable($table)->setTypes($types)->setGlobalOperation($globalOperation);
    }

    public function isGlobalOperation(): bool
    {
        return $this->globalOperation;
    }

    public function setGlobalOperation(bool $globalOperation): self
    {
        $this->globalOperation = $globalOperation;

        return $this;
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

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    public function setTypes(array $types): self
    {
        $this->types = $types;

        return $this;
    }
}
