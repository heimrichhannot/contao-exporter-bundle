<?php

namespace HeimrichHannot\ContaoExporterBundle\FrontendModule;

use Contao\BackendTemplate;
use Contao\Input;
use Contao\Message;
use Contao\Module;
use Contao\ModuleModel;
use Contao\System;
use HeimrichHannot\ContaoExporterBundle\Action\FrontendExportAction;
use HeimrichHannot\ContaoExporterBundle\Model\ExporterModel;

class ModuleFrontendExporter extends Module
{
    const NAME = 'frontendExporter';

	protected $strTemplate = 'mod_frontend_export';
    /**
     * @var ExporterModel
     */
	protected $config;

    public function __construct(ModuleModel $objModule, string $strColumn = 'main')
    {
        parent::__construct($objModule, $strColumn);
        $this->container = System::getContainer();
    }


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

    /**
     * @throws \Exception
     */
	protected function compile()
	{
	    /** @var ExporterModel config */
		$this->config = ExporterModel::findByPk($this->exporterConfig);

		if(null === $this->config)
        {
            return;
        }

        $this->Template->action = '';
        $this->Template->method = 'POST';
        $this->Template->type = $this->getExporterType();
        $this->Template->btnLabel = $this->exporterBtnLabel;

        $exportType = Input::post('export');
        if (null === $exportType)
        {
            return;
        }

        try
        {
            $frontendAction = new FrontendExportAction($this->container);
            $frontendAction->export($this->config);
        } catch (\Exception $e)
        {
            Message::addError($this->container->get('translator')->trans('huh.exporter.error.exportNotPossible'), 'huh_exporter.frontend');
        }

        $this->Template->messages = Message::generateUnwrapped('huh_exporter.frontend');
	}

    protected function getEntity()
    {
        $autoItem = Input::get('auto_item');
        if ($autoItem)
        {
            return $autoItem;
        }
        if ($id = Input::get('id'))
        {
            return $id;
        }
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
}
