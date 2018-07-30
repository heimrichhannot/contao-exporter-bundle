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
use Contao\Message;
use HeimrichHannot\ContaoExporterBundle\Exception\ExporterConfigurationException;
use HeimrichHannot\ContaoExporterBundle\Manager\ExporterManager;
use HeimrichHannot\ContaoExporterBundle\Model\ExporterModel;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;

class BackendExportAction
{
    /**
     * @var ExporterManager
     */
    private $exporterManager;

    protected $names = [];
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(ContainerInterface $container, ExporterManager $exporterManager, RequestStack $requestStack, TranslatorInterface $translator)
    {
        $this->exporterManager = $exporterManager;
        $this->requestStack    = $requestStack;
        $this->container       = $container;
        $this->translator      = $translator;
    }

    /**
     * @param $dataContainer
     * @throws \Exception
     */
    public function export($dataContainer)
    {
        $query         = $this->requestStack->getCurrentRequest()->query;
        $id            = $query->has('id') ? intval($query->get('id')) : null;
        $operationKey  = $query->has('key') ? strval($query->get('key')) : null;
        $table         = $query->has('table') ? strval($query->get('table')) : $dataContainer->table;
        $do            = $query->has('do') ? strval($query->get('do')) : null;
        $redirectRoute = $this->container->get('huh.utils.routing')->generateBackendRoute(['do' => $do], false, true);

        if (!$operationKey || !$table)
        {
            return;
        }

        if (($config = ExporterModel::findByKeyAndTable($operationKey, $table)) === null)
        {
            if (empty($_SESSION['TL_ERROR']))
            {
                Message::addError($this->translator->trans('huh.exporter.error.noConfigFound'));
                Controller::redirect($redirectRoute);
            }
        } else
        {
            $exportAction = new ExportAction($this->exporterManager);

            try
            {
                $result = $exportAction->export($config, $id);
            } catch (ExporterConfigurationException $e)
            {
                Message::addError(
                    $this->translator->trans('huh.exporter.error.configuration')
                    . $this->translator->trans('huh.exporter.error.message', ['%message%' => $e->getMessage()])
                );
            } catch (\Exception $e)
            {
                Message::addError(
                    $this->translator->trans('huh.exporter.error.exportNotPossible')
                    . $this->translator->trans('huh.exporter.error.message', ['%message%' => $e->getMessage()])
                );
                $result = false;
            }

            if (false === $result)
            {
                Controller::redirect($redirectRoute);
            }
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
    public function getGlobalOperation(string $name, $label = '', $icon = 'bundles/heimrichhannotcontaoexporter/img/icon_export.png', array $additionalUrlParameters = [], array $customOptions = [])
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