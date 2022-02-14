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
use Symfony\Contracts\EventDispatcher\Event;

class ModifyFieldValueEvent extends Event
{
    const NAME = 'huh.exporter.event.modifyfieldvalue';

    private $value;
    private $field;
    private $row;

    /**
     * @var AbstractExporter
     */
    private $context;

    public function __construct($value, $field, $row, AbstractExporter $context)
    {
        $this->value       = $value;
        $this->field       = $field;
        $this->row         = $row;
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
     * @return AbstractExporter
     */
    public function getContext(): AbstractExporter
    {
        return $this->context;
    }
}
