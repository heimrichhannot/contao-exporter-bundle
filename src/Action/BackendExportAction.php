<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoExporterBundle\Action;


use Contao\Controller;
use Contao\Input;
use Contao\Message;
use HeimrichHannot\ContaoExporterBundle\Manager\ExporterManager;
use HeimrichHannot\ContaoExporterBundle\Model\ExporterModel;

class BackendExportAction
{
    /**
     * @var ExporterManager
     */
    private $exporterManager;

    public function __construct(ExporterManager $exporterManager)
    {
        $this->exporterManager = $exporterManager;
    }

    /**
     * @param $dataContainer
     * @throws \Exception
     */
    public function export($dataContainer)
    {
        $id             = Input::get('id');
        $globalOperationKey = Input::get('key');
        $table              = Input::get('table') ?: $dataContainer->table;

        if (!$globalOperationKey || !$table)
        {
            return;
        }

        if (($config = ExporterModel::findByKeyAndTable($globalOperationKey, $table)) === null)
        {
            if (empty($_SESSION['TL_ERROR']))
            {
                Message::addError($GLOBALS['TL_LANG']['MSC']['exporter']['noConfigFound']);
                Controller::redirect($_SERVER['HTTP_REFERER']);
            }
        } else
        {
            $exportAction = new ExportAction($this->exporterManager);
            $exportAction->export($config, $id);
        }
    }

    /**
     * @param string $name
     * @param string $label
     * @param string $icon Path to the button icon
     * @param array $additionalUrlParameters Add additional url (href) parameters.
     * @param array $customOptions Add additional custom parameters
     * @return array
     */
    public static function getGlobalOperation(string $name, $label = '', $icon = '', array $additionalUrlParameters = [], array $customOptions = [])
    {
        $operation = [
            'label'      => &$label,
            'href'       => 'key=' . $name,
            'class'      => 'header_' . $name . '_entities',
            'icon'       => $icon,
            'attributes' => 'onclick="Backend.getScrollOffset()"',
        ];

        if (!empty($additionalUrlParameters))
        {
            $href = $operation['href'];
            if (array_key_exists('key', $additionalUrlParameters))
            {
                $href = '';
            }
            foreach ($additionalUrlParameters as $key => $value)
            {
                if (!empty($href))
                {
                    $href .= '&';
                }
                $href .= $key . '=' . $value;
            }
            $operation['href'] = $href;
        }

        if (!empty($customOptions))
        {
            $operation = array_merge($operation, $customOptions);
        }

        return $operation;
    }
}