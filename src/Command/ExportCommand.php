<?php

namespace HeimrichHannot\ContaoExporterBundle\Command;

use Contao\CoreBundle\Framework\ContaoFramework;
use HeimrichHannot\ContaoExporterBundle\Action\ExportAction;
use HeimrichHannot\ContaoExporterBundle\Model\ExporterModel;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ExportCommand extends Command
{
    protected static $defaultName = 'huh:exporter:export';

    /**
     * @var SymfonyStyle
     */
    private $io;

    private InputInterface  $input;
    private ContaoFramework $contaoFramework;
    private Utils           $utils;
    private ExportAction    $exportAction;

    public function __construct(ContaoFramework $contaoFramework, Utils $utils, ExportAction $exportAction)
    {
        parent::__construct();
        $this->contaoFramework = $contaoFramework;
        $this->utils = $utils;
        $this->exportAction = $exportAction;
    }


    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Runs a given exporter config on the command line.')
            ->addArgument('exporterConfig', InputArgument::REQUIRED, 'The exporter config id');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->contaoFramework->initialize();
        $this->input = $input;
        $this->io      = new SymfonyStyle($input, $output);

        if ($this->export())
        {
            $this->io->success('Export finished');
        }

        return 0;
    }

    protected function export(): bool
    {
        $exporterConfigId = $this->input->getArgument('exporterConfig');

        /** @var ExporterModel $exporterConfig */
        if (null === ($exporterConfig = $this->utils->model()->findModelInstanceByPk(
            'tl_exporter', $exporterConfigId)))
        {
            $this->io->error('Exporter config with id ' . $exporterConfigId . ' not found.');
            return false;
        }

        if ($exporterConfig->language)
        {
            $language = $GLOBALS['TL_LANGUAGE'];

            $GLOBALS['TL_LANGUAGE'] = $exporterConfig->language;
        }

        $this->exportAction->export($exporterConfig);

        if ($exporterConfig->language)
        {
            $GLOBALS['TL_LANGUAGE'] = $language;
        }

        return true;
    }
}
