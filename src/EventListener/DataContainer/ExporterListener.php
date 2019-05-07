<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoExporterBundle\EventListener\DataContainer;


use Contao\BackendUser;
use Contao\Controller;
use Contao\Database;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\StringUtil;
use Contao\System;
use HeimrichHannot\ContaoExporterBundle\Manager\ExporterManager;
use HeimrichHannot\ContaoExporterBundle\Exporter\AbstractExporter;
use HeimrichHannot\ContaoExporterBundle\Model\ExporterModel;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ExporterListener
{
    /**
     * @var ExporterManager
     */
    private $exporterManager;
    /**
     * @var ContainerInterface
     */
    private $container;
    
    public function __construct(ContainerInterface $container, ExporterManager $exporterManager)
    {
        $this->exporterManager = $exporterManager;
        $this->container       = $container;
    }
    
    
    public function checkPermission()
    {
        if (BackendUser::getInstance()->isAdmin) {
            return;
        }
    }
    
    /**
     * Return exporter options for selected file type.
     *
     * @param DC_Table $dc
     *
     * @return mixed
     */
    public function getExporterClasses(DC_Table $dc)
    {
        return array_combine(
            array_map(
                function ($className) { return str_replace('\\', '_', $className ); },
                $this->exporterManager->getExporterByFileType($dc->activeRecord->fileType)
            ),
            $this->exporterManager->getExporterByFileType($dc->activeRecord->fileType)
        );
    }
    
    /**
     * Returns file type options
     *
     * @return array
     */
    public function getFileType()
    {
        return array_keys($this->exporterManager->getExporterFileTypes());
    }
    
    public function getTableFields($dataContainer)
    {
        if (($exporterConfig = ExporterModel::findByPk($dataContainer->id)) === null) {
            return [];
        }
        
        
        $blnUnformatted = $exporterConfig->addUnformattedFields && $exporterConfig->type != AbstractExporter::TYPE_ITEM
                          && $exporterConfig->fileType != EXPORTER_FILE_TYPE_MEDIA;
        $blnJoins       = $exporterConfig->addJoinTables;
        
        $arrOptions = $this->doGetTableFields($exporterConfig->linkedTable, $blnUnformatted, $blnJoins);
        
        if ($exporterConfig->addJoinTables) {
            foreach ($this->getJoinTables($exporterConfig) as $strTable) {
                $arrOptions = array_merge($arrOptions, $this->doGetTableFields($strTable, $blnUnformatted, $blnJoins));
            }
        }
        
        return $arrOptions;
    }
    
    public function doGetTableFields($strTable, $blnIncludeUnformatted = false, $blnPrefixTableName = false)
    {
        $arrOptions = [];
        
        if (!$strTable) {
            return [];
        }
        
        Controller::loadDataContainer($strTable);
        
        $arrFields = $GLOBALS['TL_DCA'][$strTable]['fields'];
        
        if (!is_array($arrFields) || empty($arrFields)) {
            return $arrOptions;
        }
        
        foreach ($arrFields as $strField => $arrData) {
            $arrOptions[$strTable . '.' . $strField] = ($blnPrefixTableName ? $strTable . '.' : '') . $strField;
        }
        
        if ($blnIncludeUnformatted) {
            $arrOptionsRawKeys = array_map(
                function ($val) {
                    return $val . EXPORTER_RAW_FIELD_SUFFIX;
                },
                array_keys($arrOptions)
            );
            
            $arrOptionsRawValues = array_map(
                function ($val) {
                    return $val . $GLOBALS['TL_LANG']['MSC']['exporter']['unformatted'];
                },
                array_values($arrOptions)
            );
            
            $arrOptions += array_combine($arrOptionsRawKeys, $arrOptionsRawValues);
        }
        
        return $arrOptions;
    }
    
    public function getJoinTables($exporterConfig)
    {
        $arrResult = [];
        foreach (StringUtil::deserialize($exporterConfig->joinTables, true) as $arrRow) {
            if (isset($arrRow['joinTable'])) {
                $arrResult[] = $arrRow['joinTable'];
            }
        }
        
        return $arrResult;
    }
    
    /**
     * Searches through all backend modules to find the linked tables for the selected global operation key
     *
     * @param DataContainer $dataContainer
     *
     * @return array
     */
    public function getLinkedTablesAsOptions(DataContainer $dataContainer)
    {
        $arrTables             = [];
        $strGlobalOperationKey = $dataContainer->activeRecord->globalOperationKey;
        
        switch ($dataContainer->activeRecord->type) {
            case AbstractExporter::TYPE_LIST:
                if ($strGlobalOperationKey) {
                    foreach ($GLOBALS['BE_MOD'] as $arrSection) {
                        foreach ($arrSection as $strModule => $arrModule) {
                            foreach ($arrModule as $strKey => $varValue) {
                                if ($strKey === $strGlobalOperationKey) {
                                    $arrTables[$strModule] = $arrModule['tables'];
                                }
                            }
                        }
                    }
                }
                break;
            default:
                $arrTables = $this->container->get('huh.utils.dca')->getDataContainers();
        }
        
        return $arrTables;
    }
    
    /**
     * Get all tables for possible join
     *
     * @return array
     */
    public function getAllTablesAsOptions()
    {
        return Database::getInstance()->listTables();
    }
    
    /**
     * Searches through all backend modules to find global operation keys and returns a filtered list
     *
     * @return array
     */
    public function getGlobalOperationKeysAsOptions()
    {
        $arrGlobalOperations = [];
        $arrSkipKeys         = ['callback', 'generate', 'icon', 'import', 'javascript', 'stylesheet', 'table', 'tables'];
        
        foreach ($GLOBALS['BE_MOD'] as $arrSection) {
            foreach ($arrSection as $arrModule) {
                foreach ($arrModule as $strKey => $varValue) {
                    if (!in_array($strKey, $arrGlobalOperations) && !in_array($strKey, $arrSkipKeys)) {
                        $arrGlobalOperations[] = $strKey;
                    }
                }
            }
        }
        sort($arrGlobalOperations);
        
        return $arrGlobalOperations;
    }
    
    /**
     * Return available PDF templates for the pdf exporter
     *
     * @return mixed
     */
    public function getPdfExporterTemplates()
    {
        return Controller::getTemplateGroup('exporter_pdf_');
    }
    
    /**
     * @param DataContainer $dataContainer
     *
     * @return array
     * @throws \Exception
     */
    public function getTableArchives(DataContainer $dataContainer)
    {
        $arrOptions = [];
        
        if (($linkedTable = $dataContainer->activeRecord->linkedTable) && $GLOBALS['TL_DCA'][$linkedTable]['config']['ptable']) {
            Controller::loadDataContainer($linkedTable);
            System::loadLanguageFile($linkedTable);
            if (!isset($GLOBALS['TL_DCA'][$linkedTable]['config']['ptable'])) {
                throw new \Exception('No parent table found for ' . $linkedTable);
            }
            $objArchives = $this->container->get('huh.utils.model')->findAllModelInstances(
                $GLOBALS['TL_DCA'][$linkedTable]['config']['ptable'],
                ['order' => 'title ASC']
            );
            
            if ($objArchives !== null) {
                while ($objArchives->next()) {
                    $arrOptions[$objArchives->id] = $objArchives->title;
                }
            }
        }
        
        return $arrOptions;
    }
    
}