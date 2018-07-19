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
    private $exporter;

    public function __construct(array $headerFields, AbstractTableExporter $exporter)
    {
        $this->headerFields = $headerFields;
        $this->exporter     = $exporter;
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
    public function getExporter(): AbstractTableExporter
    {
        return $this->exporter;
    }

    /**
     * @param AbstractTableExporter $exporter
     */
    public function setExporter(AbstractTableExporter $exporter): void
    {
        $this->exporter = $exporter;
    }


}