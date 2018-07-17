<?php

namespace HeimrichHannot\ContaoExporterBundle\FrontendModule;

use Contao\BackendTemplate;
use Contao\Module;
use Contao\System;
use HeimrichHannot\ContaoExporterBundle\Model\ExporterModel;
use HeimrichHannot\Exporter\Concrete\CsvExporter;
use HeimrichHannot\Exporter\Concrete\MediaExporter;
use HeimrichHannot\Exporter\Concrete\PdfExporter;
use HeimrichHannot\Exporter\Concrete\XlsExporter;

class ModuleFrontendExporter extends Module
{
	protected $strTemplate = 'mod_frontend_export';
    /**
     * @var ExporterModel
     */
	protected $config;


    public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . $GLOBALS['TL_LANG']['FMD']['frontendExporter'][0] . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		return parent::generate();
	}

	protected function compile()
	{
	    /** @var ExporterModel config */
		$this->config = ExporterModel::findByPk($this->exporterConfig);
		
		if(null === $this->config)
        {
            return;
        }
	    
	    $entity = $this->getEntity();
        
        $this->Template->action = '';
        $this->Template->method = 'POST';
        $this->Template->type = $this->getExporterType();
        
        $this->Template->btnLabel = $this->exporterBtnLabel;



        if(null === ($exportType = System::getContainer()->get("huh.request")->getPost('export')))
        {
            return;
        }

        if(!class_exists($this->config->exporterClass))
        {
            return;
        }
        
        $exporter = new $this->config->exporterClass($this->config);
        
        
        if(null === $exporter)
        {
            return;
        }
        
        $exporter->export($entity, deserialize($this->config->tableFieldsForExport,true));
	}

    protected function getEntity()
    {
        return $this->config->linkedTable;
	}

    protected function getExporterType()
    {
        return $this->config->fileType;
	}

	public static function getGlobalOperation($strName, $strLabel = '', $strIcon = '')
	{
		$arrOperation = [
			'label'      => &$strLabel,
			'href'       => 'exportType=list&key=' . $strName,
			'class'      => 'header_' . $strName . '_entities',
			'icon'       => $strIcon,
			'attributes' => 'onclick="Backend.getScrollOffset()"'];

		return $arrOperation;
	}

	public static function getOperation($strName, $strLabel = '', $strIcon = '')
	{
		$arrOperation = [
			'label'      => &$strLabel,
			'href'       => 'exportType=item&key=' . $strName,
			'icon'       => $strIcon,];

		return $arrOperation;
	}

	public static function getBackendModule()
	{
		return ['HeimrichHannot\Exporter\ModuleExporter', 'export'];
	}

}
