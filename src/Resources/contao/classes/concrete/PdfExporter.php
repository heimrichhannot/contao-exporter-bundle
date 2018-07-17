<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 *
 * @author  Oliver Janke <o.janke@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Exporter\Concrete;

use HeimrichHannot\Exporter\Exporter;
use HeimrichHannot\FieldPalette\FieldPaletteModel;
use HeimrichHannot\Haste\Pdf\PdfTemplate;
use HeimrichHannot\Haste\Util\Files;

class PdfExporter extends Exporter
{
    protected function doExport($objEntity = null, array $arrFields = [])
    {
        switch ($this->type)
        {
            case Exporter::TYPE_ITEM:
                $arrMargins = deserialize($this->pdfMargins, true);

                if (count($arrMargins) > 0)
                {
                    $objPdf = new PdfTemplate(
                        'A4', PdfTemplate::ORIENTATION_PORTRAIT, $arrMargins['left'], $arrMargins['right'], $arrMargins['top'], $arrMargins['bottom']
                    );
                }
                else
                {
                    $objPdf = new PdfTemplate();
                }

                // template
                if ($this->pdfBackground)
                {
                    $objPdf->addTemplatePdf(Files::getPathFromUuid($this->objConfig->pdfBackground));
                }

                // fonts
                if (($objPdfFonts = FieldPaletteModel::findByPidAndTableAndField($this->objConfig->id, 'tl_exporter', 'pdfFonts')) !== null)
                {
                    while ($objPdfFonts->next())
                    {
                        switch ($objPdfFonts->exporter_pdfFonts_fontWeight)
                        {
                            case 'B':
                                $strMethod = 'addBoldFont';
                                break;
                            case 'I':
                                $strMethod = 'addItalicFont';
                                break;
                            case 'BI':
                                $strMethod = 'addBoldItalicFont';
                                break;
                            default:
                                $strMethod = 'addRegularFont';
                        }

                        $objPdf->{$strMethod}(
                            $objPdfFonts->exporter_pdfFonts_fontName,
                            Files::getPathFromUuid($objPdfFonts->exporter_pdfFonts_file)
                        );
                    }
                }

                $objTemplate         = new \FrontendTemplate($this->pdfTemplate ?: 'exporter_pdf_item_default');
                $objTemplate->raw    = $objEntity->row();

                foreach ($objEntity->row() as $strName => $arrValue)
                {
                    $objTemplate->{$strName} = $arrValue;
                }

                // skip fields
                $arrSkipFields = array_map(function($val) {
                    list($strTable, $strField) = explode('.', $val);

                    return $strField;
                }, deserialize($this->skipFields, true));

                foreach ($arrSkipFields as $strName)
                {
                    unset($arrFields[$strName]);
                }

                // skip labels
                $arrSkipLabels = array_map(function($val) {
                    list($strTable, $strField) = explode('.', $val);

                    return $strField;
                }, deserialize($this->skipLabels, true));

                foreach ($arrSkipLabels as $strName)
                {
                    unset($arrFields[$strName]['label']);
                }

                $objTemplate->fields = $arrFields;

                // css
                $arrCss = deserialize($this->pdfCss, true);
                $strCss = '';

                if (!empty($arrCss))
                {
                    $strCss = implode('', array_map(function($val) {
                        return file_get_contents(Files::getPathFromUuid($val));
                    }, $arrCss));
                }

                $objPdf->writeHtml($objTemplate->parse(), $strCss);

                return $objPdf;

                break;
            case Exporter::TYPE_LIST:
                break;
        }
    }

    public function exportToDownload($objResult)
    {
        $objResult->sendToBrowser($this->strFilename, 'D');
    }

    public function exportToFile($objResult)
    {
        if ($this->strFileDir && $this->strFilename)
        {
            $objResult->saveToFile($this->strFileDir, $this->strFilename);
        }
        else
        {
            throw new \Exception('No valid path for exporter!');
        }
    }
}