<?php

/*
 * Copyright (c) 2022 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\ContaoExporterBundle\Manager;

use HeimrichHannot\ContaoExporterBundle\ExportOperation\ExportOperationInterface;

class ExporterOperationManager
{
    private iterable $operations;

    private array $tableIndex;

    public function __construct(iterable $operations)
    {
        $this->operations = $operations;
    }

    public function hasRegisteredTable(string $table): bool
    {
        if (!isset($this->tableIndex)) {
            $this->initializeIndices();
        }

        return isset($this->tableIndex[$table]);
    }

    /**
     * @return array|ExportOperationInterface[]
     */
    public function getTableOperations(string $table): array
    {
        return $this->tableIndex[$table] ?? [];
    }

    public function initializeIndices(): void
    {
        /** @var ExportOperationInterface $operation */
        foreach ($this->operations as $operation) {
            $this->tableIndex[$operation->getConfiguration()->getTable()][] = $operation;
        }
    }
}
