<?php

/*
 * Copyright (c) 2022 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\ContaoExporterBundle\ExportOperation;

use HeimrichHannot\ContaoExporterBundle\ExporterConfiguration\ExporterConfiguration;

interface ExportOperationInterface
{
    public function getType(): string;

    public function getConfiguration(): ExportOperationConfiguraton;

    public function defaultConfiguration(): ?ExporterConfiguration;
}
