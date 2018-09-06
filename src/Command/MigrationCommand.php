<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoExporterBundle\Command;


use HeimrichHannot\ContaoExporterBundle\Exporter\AbstractExporter;
use HeimrichHannot\ContaoExporterBundle\Exporter\Concrete\CsvExporter;
use HeimrichHannot\ContaoExporterBundle\Exporter\Concrete\ExcelExporter;
use HeimrichHannot\ContaoExporterBundle\Exporter\Concrete\MediaExporter;
use HeimrichHannot\ContaoExporterBundle\Exporter\Concrete\PdfExporter;
use HeimrichHannot\ContaoExporterBundle\Model\ExporterModel;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class MigrationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('huh:exporter:migration')
            ->setDescription('Migrate your existing exporter configurations from module to bundle.')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, "Run command without making changes to the database.")
            ->setHelp("This command migrates the exporter class names from the exporter module to the exporter bundle. It also changes the formhybrid export types to item type.")
        ;
    }

    /**
     * Executes the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $framework = $this->getContainer()->get('contao.framework');
        if (!$framework->isInitialized()) {
            $framework->initialize();
        }
        $io = new SymfonyStyle($input, $output);
        $dryRun = $input->getOption('dry-run');
        $io->title('Contao Exporter Migration');

        $exporters = ExporterModel::findAll();

        $io->writeln("Found ".$exporters->count().' exporter configurations.');

        if (!$output->isQuiet() && !$output->isVerbose())
        {
            $io->newLine();
            $io->progressStart($exporters->count());
        }

        $updatedCount = 0;

        /** @var ExporterModel $exporter */
        foreach ($exporters as $exporter)
        {
            if ($output->isVerbose())
            {
                $io->newLine();
                $output->writeln("Migration exporter config ".$exporter->title.' (ID: '.$exporter->id.')');
            }
            if (!$output->isQuiet() && !$output->isVerbose())
            {
                $io->progressAdvance();
            }

            $updated = false;
            if ($exporter->exporterClass == 'HeimrichHannot\Exporter\Concrete\XlsExporter')
            {
                $exporter->exporterClass = ExcelExporter::class;
                $updated = true;
                if ($output->isVerbose())
                {
                    $output->writeln("Updated exporter class to ".ExcelExporter::class);
                }
            }
            if ($exporter->exporterClass == 'HeimrichHannot\Exporter\Concrete\CsvExporter')
            {
                $exporter->exporterClass = CsvExporter::class;
                $updated = true;
                if ($output->isVerbose())
                {
                    $output->writeln("Updated exporter class to ".CsvExporter::class);
                }
            }
            if ($exporter->exporterClass == 'HeimrichHannot\Exporter\Concrete\MediaExporter')
            {
                $exporter->exporterClass = MediaExporter::class;
                $updated = true;
                if ($output->isVerbose())
                {
                    $output->writeln("Updated exporter class to ".MediaExporter::class);
                }
            }
            if ($exporter->exporterClass == 'HeimrichHannot\Exporter\Concrete\PdfExporter')
            {
                $exporter->exporterClass = PdfExporter::class;
                $updated = true;
                if ($output->isVerbose())
                {
                    $output->writeln("Updated exporter class to ".PdfExporter::class);
                }
            }

            if ($exporter->type == 'formhybrid')
            {
                $exporter->type = AbstractExporter::TYPE_ITEM;
                if ($output->isVerbose())
                {
                    $output->writeln("Changed formhybrid type to item type.");
                }
            }

            if ($exporter->fileType == "xls")
            {
                $exporter->fileType = "xlsx";
                $updated = true;

                if ($output->isVerbose())
                {
                    $output->writeln("Converted xls to xlsx.");
                }
            }

            if (true === $updated)
            {
                $updatedCount++;
            }

            if (true === $updated)
            {
                if (false === $dryRun)
                {
                    $exporter->save();
                    if ($output->isVerbose())
                    {
                        $output->writeln("Saved updated configuration to database.");
                    }
                }
                else {
                    if ($output->isVerbose()) {
                        $output->writeln("Skipped saving due dry run option.");
                    }
                }
            }
            else {
                if ($output->isVerbose()) {
                    $output->writeln("Found nothing to update.");
                }
            }
        }
        if (!$output->isQuiet() && !$output->isVerbose())
        {
            $io->progressFinish();
        }

        $io->newLine();
        $io->success("Migration finished. Updated ".$updatedCount.' exporter configurations.');
        return 0;
    }
}