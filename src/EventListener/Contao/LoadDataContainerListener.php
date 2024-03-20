<?php

/*
 * Copyright (c) 2022 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\ContaoExporterBundle\EventListener\Contao;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use HeimrichHannot\ContaoExporterBundle\Action\BackendExportAction;
use HeimrichHannot\ContaoExporterBundle\Manager\ExporterOperationManager;

/**
 * @Hook("loadDataContainer")
 */
class LoadDataContainerListener
{
    private ExporterOperationManager $operationManager;
    private BackendExportAction      $backendExportAction;

    public function __construct(ExporterOperationManager $operationManager, BackendExportAction $backendExportAction)
    {
        $this->operationManager = $operationManager;
        $this->backendExportAction = $backendExportAction;
    }

    public function __invoke(string $table): void
    {
        if (!$this->operationManager->hasRegisteredTable($table)) {
            return;
        }

        $operations = $this->operationManager->getTableOperations($table);

        foreach ($operations as $operation) {
            if ($operation->getConfiguration()->isGlobalOperation()) {
                foreach ($operation->getConfiguration()->getTypes() as $type) {
                    $GLOBALS['TL_DCA'][$table]['list']['global_operations'][$operation->getType().'_'.$type] = $this->backendExportAction
                        ->getGlobalOperation(
                            $operation->getType().'_'.$type,
                            $GLOBALS['TL_LANG']['MSC'][$operation->getType().'_'.$type],
                        );
                }
            }
        }
    }
}
