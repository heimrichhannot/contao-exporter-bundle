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


use HeimrichHannot\ContaoExporterBundle\Exporter\AbstractExporter;
use HeimrichHannot\ContaoExporterBundle\Model\ExporterModel;
use Symfony\Component\EventDispatcher\Event;

class BeforeBuildQueryEvent extends Event
{
    const NAME = 'huh.exporter.event.before_build_query';
    /**
     * @var ExporterModel
     */
    private $config;
    /**
     * @var array
     */
    private $exportFields;
    /**
     * @var array
     */
    private $joinTables;
    /**
     * @var array
     */
    private $wheres;
    /**
     * @var string
     */
    private $orderBy;
    /**
     * @var AbstractExporter
     */
    private $exporter;

    public function __construct(ExporterModel $config, array $exportFields, array $joinTables, array $wheres, string $orderBy, AbstractExporter $exporter)
    {
        $this->config = $config;
        $this->exportFields = $exportFields;
        $this->joinTables = $joinTables;
        $this->wheres = $wheres;
        $this->orderBy = $orderBy;
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
     * @return array
     */
    public function getExportFields(): array
    {
        return $this->exportFields;
    }

    /**
     * @param array $exportFields
     */
    public function setExportFields(array $exportFields): void
    {
        $this->exportFields = $exportFields;
    }

    /**
     * @return array
     */
    public function getJoinTables(): array
    {
        return $this->joinTables;
    }

    /**
     * @param array $joinTables
     */
    public function setJoinTables(array $joinTables): void
    {
        $this->joinTables = $joinTables;
    }

    /**
     * @return array
     */
    public function getWheres(): array
    {
        return $this->wheres;
    }

    /**
     * @param array $wheres
     */
    public function setWheres(array $wheres): void
    {
        $this->wheres = $wheres;
    }

    /**
     * @return string
     */
    public function getOrderBy(): string
    {
        return $this->orderBy;
    }

    /**
     * @param string $orderBy
     */
    public function setOrderBy(string $orderBy): void
    {
        $this->orderBy = $orderBy;
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