<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 *
 * @author  Oliver Janke <o.janke@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\ContaoExporterBundle\Exporter\Concrete;

use Contao\Model;
use Contao\StringUtil;
use HeimrichHannot\ContaoExporterBundle\Exporter\AbstractExporter;
use HeimrichHannot\ContaoExporterBundle\Exporter\ExportTargetDownloadInterface;
use HeimrichHannot\ContaoExporterBundle\Exporter\ExportTargetFileInterface;
use HeimrichHannot\ContaoExporterBundle\Exporter\ExportTypeItemInterface;
use HeimrichHannot\UtilsBundle\Pdf\PdfWriter;

class PdfExporter extends AbstractExporter implements ExportTypeItemInterface
{
    protected function doExport($entity = null, array $fields = [])
    {
        $entity = $this->getEntity($entity);
        $fields = $this->prepareItemFields($fields, $entity);
        return $this->exportItem($entity, $fields);
    }

    /**
     * @param PdfWriter $pdfWriter
     * @param string $fileDir
     * @param string $fileName
     * @return mixed|void
     */
    public function exportToDownload($pdfWriter, string $fileDir, string $fileName)
    {
        $this->createPdf($pdfWriter, $fileName, true);
    }

    /**
     * @param PdfWriter $pdfWriter
     * @param string $fileDir
     * @param string $fileName
     * @return mixed|void
     * @throws \Exception
     */
    public function exportToFile($pdfWriter, string $fileDir, string $fileName)
    {
        $this->createPdf($pdfWriter, $fileName, false);

//        if (!empty($fileDir) && !empty($fileName))
//        {
//            $pdfWriter->saveToFile($fileDir, $fileName);
//        }
//        else
//        {
//            throw new \Exception('No valid path for exporter!');
//        }
    }

    public function createPdf(PdfWriter $pdfWriter, string $fileName, bool $download)
    {
        $pdfWriter->setFileName($fileName);
        $pdfWriter->generate($download);
    }

    /**
     * Return a list of supported file types
     *
     * Example: ['csv','xslt']
     *
     * @return array
     */
    public function getSupportedFileTypes(): array
    {
        return ['pdf'];
    }

    /**
     * Export the item
     *
     * @param Model $entity
     * @param array $fields
     * @return mixed
     */
    public function exportItem($entity, array $fields = [])
    {
        $pdfConfig = [
            'format'      => 'A4',
            'orientation' => 'P',
        ];

        $pdfConfig = $this->setPdfMargins($pdfConfig);

        if (!empty($fields))
        {
            $rawFields = $fields;

            list($skipFields, $fields, $skipLabels) = $this->setPdfFields($fields);

            $pdfTemplate = $this->config->pdfTemplate ?: 'exporter_pdf_item_default';
            $pdfTemplate = $this->container->get('huh.utils.template')->getTemplate($pdfTemplate);
            $htmlContent = $this->container->get('twig')->render($pdfTemplate, [
                'raw' => $rawFields,
                'fields' => $fields,
                'skipFields' => $skipFields,
                'skipLabels' => $skipLabels,
            ]);
        }
        else {
            $htmlContent = '';
        }

        $htmlContent = $this->setPdfCssStyles($htmlContent);


        $pdfWriter = $this->container->get('huh.utils.pdf.writer')->mergeConfig($pdfConfig);
        // PDF Template
        if ($this->config->pdfBackground)
        {
            $pdfWriter->setTemplate($templatePath = $this->container->get('huh.utils.file')->getPathFromUuid($this->config->pdfBackground));
        }
        $this->setPdfFonts($pdfWriter);
        $pdfWriter->setHtml($htmlContent);

        return $pdfWriter;
    }

    /**
     * @param $pdfConfig
     * @return mixed
     */
    protected function setPdfMargins($pdfConfig)
    {
        $arrMargins = StringUtil::deserialize($this->config->pdfMargins, true);

        if (count($arrMargins) > 0)
        {
            if (!empty($arrMargins['left']))
            {
                $pdfConfig['margin_left'] = $arrMargins['left'];
            }
            if (!empty($arrMargins['right']))
            {
                $pdfConfig['margin_right'] = $arrMargins['right'];
            }
            if (!empty($arrMargins['top']))
            {
                $pdfConfig['margin_top'] = $arrMargins['top'];
            }
            if (!empty($arrMargins['bottom']))
            {
                $pdfConfig['margin_bottom'] = $arrMargins['bottom'];
            }
        }
        return $pdfConfig;
    }

    /**
     * @param $htmlContent
     * @return string
     */
    protected function setPdfCssStyles($htmlContent): string
    {
        $cssStyles = '';
        if ($this->config->pdfCss)
        {
            $cssPaths = deserialize($this->config->pdfCss, true);
            if (!empty($cssPaths))
            {
                $cssStyles = implode('', array_map(function ($uuid) {
                    return file_get_contents($this->container->get('huh.utils.file')->getPathFromUuid($uuid));
                }, $cssPaths));
            }
            if (!empty($cssStyles))
            {
                $htmlContent = '<style>' . $cssStyles . '</style>' . $htmlContent;
            }
        }
        return $htmlContent;
    }

    /**
     * @param $pdfWriter
     */
    protected function setPdfFonts($pdfWriter): void
    {
        if ($this->config->pdfFontDirectories)
        {
            $fontPathUUids = StringUtil::deserialize($this->config->pdfFontDirectories);
            if (!empty($fontPathUUids))
            {
                $fontPaths = [];
                foreach ($fontPathUUids as $uuid)
                {
                    $path = $this->container->get('huh.utils.file')->getPathFromUuid($uuid, false);
                    if ($path)
                    {
                        $fontPaths[] = $path;
                    }
                }
                $pdfWriter->addFontDirectories($fontPaths);
            }
        }
    }

    /**
     * @param array $fields
     * @return array
     */
    protected function setPdfFields(array $fields): array
    {
        $skipFields = array_map(
            function ($val) {
                list($strTable, $field) = explode('.', $val);

                return $field;
            }, StringUtil::deserialize($this->config->skipFields, true)
        );

        foreach ($skipFields as $name)
        {
            unset($fields[$name]);
        }

        // skip labels
        $skipLabels = array_map(function ($val) {
            list($strTable, $field) = explode('.', $val);

            return $field;
        }, StringUtil::deserialize($this->config->skipLabels, true));

        foreach ($skipLabels as $name)
        {
            unset($fields[$name]['label']);
        }
        return [$skipFields, $fields, $skipLabels];
    }
}