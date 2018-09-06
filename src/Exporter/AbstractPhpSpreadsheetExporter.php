<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 *
 * @author  Oliver Janke <o.janke@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\ContaoExporterBundle\Exporter;

use Box\Spout\Common\Type;
use Box\Spout\Writer\WriterFactory;
use Contao\System;
use HeimrichHannot\ContaoExporterBundle\Event\ModifyFieldValueEvent;
use HeimrichHannot\UtilsBundle\Driver\DC_Table_Utils;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

abstract class AbstractPhpSpreadsheetExporter extends AbstractTableExporter
{
    protected $arrExportFields = [];

    /**
     * @param null $entity
     * @param array $fields
     * @return mixed
     */
    protected function doExport($entity = null, array $fields = [])
    {
        return $this->exportList($this->getEntities($entity));
    }

    /**
     * @param $databaseResult
     * @return array
     */
    public function exportList($databaseResult)
    {
        $table  = $this->config->linkedTable;
        $arrDca = &$GLOBALS['TL_DCA'][$table];
        $formattedRows = [];

        // header
        if ($this->config->addHeaderToExportTable && is_array($this->headerFields)) {
            $formattedRows[] = $this->headerFields;
        }

        // body
        if ($databaseResult->numRows > 0) {
            while ($databaseResult->next()) {
                $formattedRow = [];
                $row = $databaseResult->row();

                $dcTable               = new DC_Table_Utils($table);
                $dcTable->activeRecord = $databaseResult;
                $strId                 = $table . '.id';
                $dcTable->id           = $databaseResult->{$strId};

                // trigger onload_callback since these could modify the dca
                if (is_array($arrDca['config']['onload_callback'])) {
                    foreach ($arrDca['config']['onload_callback'] as $callback) {
                        if (is_array($callback)) {
                            if (!isset($arrOnload[implode(',', $callback)])) {
                                $arrOnload[implode(',', $callback)] = 0;
                            }
                            System::importStatic($callback[0])->{$callback[1]}($dcTable);
                        } elseif (is_callable($callback)) {
                            $callback($dcTable);
                        }
                    }
                }

                foreach ($row as $key => $value) {
                    $strField = str_replace($table . '.', '', $key);
                    $value    = $this->config->localizeFields ? $this->container->get('huh.utils.form')->prepareSpecialValueForOutput(
                        $strField, $value, $dcTable
                    ) : $value;

                    if (is_array($value)) {
                        $value = $this->container->get('huh.utils.array')->flattenArray($value);
                    }

                    $event = $this->dispatcher->dispatch(ModifyFieldValueEvent::NAME, new ModifyFieldValueEvent($value, $strField, $row, $this));

                    $formattedRow[] = html_entity_decode($event->getValue());
                }

                $formattedRows[] = $formattedRow;
            }
        }

        return $formattedRows;
    }

    /**
     * @param array $formattedRows
     * @param string $fileDir
     * @param string $fileName
     */
    public function exportToDownload($formattedRows, string $fileDir, string $fileName)
    {
        $writer = $this->getDocumentWriter();

        $writer->openToBrowser($fileName);
        $writer->addRows($formattedRows);

        $writer->close();

        exit();
    }

    /**
     * @param array $formattedRows
     * @param string $fileDir
     * @param string $fileName
     * @return bool
     */
    public function exportToFile($formattedRows, string $fileDir, string $fileName)
    {
        $writer = $this->getDocumentWriter();

        $writer->openToFile(System::getContainer()->get('huh.utils.container')->getProjectDir() . '/' . $fileDir . '/' . $fileName);
        $writer->addRows($formattedRows);

        try {
            $writer->close();
        } catch (\Exception $e) {
            return false;
        }

        return true;

    }

    protected function getDocumentWriter() {
        return WriterFactory::create($this->config->fileType === 'xlsx' ? Type::XLSX : Type::CSV);
    }

    public function processHeaderRow(int $col)
    {
    }

    public function processBodyRow(int $col)
    {
    }

    /**
     * Update the responce
     *
     * @param BinaryFileResponse $response
     */
    protected function beforeResponce(BinaryFileResponse &$response)
    {
    }


}
