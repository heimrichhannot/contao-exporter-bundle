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


use HeimrichHannot\ContaoExporterBundle\Exporter\AbstractTableExporter;
use Symfony\Component\EventDispatcher\Event;

class ModifyHeaderFieldsEvent extends Event
{
    const NAME = "huh.exporter.event.modifyheaderfields";
    /**
     * @var array
     */
    private $headerFields;
    /**
     * @var AbstractTableExporter
     */
    private $context;

    public function __construct(array $headerFields, AbstractTableExporter $context)
    {
        $this->headerFields = $headerFields;
        $this->context = $context;
    }

    /**
     * @return array
     */
    public function getHeaderFields(): array
    {
        return $this->headerFields;
    }

    /**
     * @param array $headerFields
     */
    public function setHeaderFields(array $headerFields): void
    {
        $this->headerFields = $headerFields;
    }

    /**
     * @return AbstractTableExporter
     */
    public function getContext(): AbstractTableExporter
    {
        return $this->context;
    }

    /**
     * @param AbstractTableExporter $context
     */
    public function setContext(AbstractTableExporter $context): void
    {
        $this->context = $context;
    }


}