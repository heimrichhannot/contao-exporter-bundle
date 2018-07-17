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
use Symfony\Component\EventDispatcher\Event;

class ModifyFieldValueEvent extends Event
{
    const NAME = "huh.exporter.event.modifyfieldvalue";

    private $value;
    private $field;
    private $row;
    /**
     * @var int
     */
    private $columnIndex;
    /**
     * @var int
     */
    private $rowIndex;
    /**
     * @var AbstractExporter
     */
    private $context;

    public function __construct($value, $field, $row, int $columnIndex, int $rowIndex, AbstractExporter $context)
    {
        $this->value       = $value;
        $this->field       = $field;
        $this->row         = $row;
        $this->columnIndex = $columnIndex;
        $this->rowIndex    = $rowIndex;
        $this->context     = $context;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param mixed $field
     */
    public function setField($field): void
    {
        $this->field = $field;
    }

    /**
     * @return mixed
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * @param mixed $row
     */
    public function setRow($row): void
    {
        $this->row = $row;
    }

    /**
     * @return int
     */
    public function getColumnIndex(): int
    {
        return $this->columnIndex;
    }

    /**
     * @param int $columnIndex
     */
    public function setColumnIndex(int $columnIndex): void
    {
        $this->columnIndex = $columnIndex;
    }

    /**
     * @return int
     */
    public function getRowIndex(): int
    {
        return $this->rowIndex;
    }

    /**
     * @param int $rowIndex
     */
    public function setRowIndex(int $rowIndex): void
    {
        $this->rowIndex = $rowIndex;
    }

    /**
     * @return AbstractExporter
     */
    public function getContext(): AbstractExporter
    {
        return $this->context;
    }
}