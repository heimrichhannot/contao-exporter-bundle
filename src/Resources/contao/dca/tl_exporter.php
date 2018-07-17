<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 *
 * @package exporter
 * @author  Oliver Janke <o.janke@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

/**
 * Table tl_exporter
 */
$GLOBALS['TL_DCA']['tl_exporter'] = [

    // Config
    'config'      => [
        'dataContainer'    => 'Table',
        'enableVersioning' => true,
        'onload_callback'  => [
            ['tl_exporter', 'checkPermission'],
        ],
        'sql'              => [
            'keys' => [
                'id' => 'primary',
            ],
        ],

    ],

    // List
    'list'        => [
        'sorting'           => [
            'mode'        => 1,
            'flag'        => 11,
            'panelLayout' => 'filter;search,limit',
            'fields'      => ['fileType'],
        ],
        'label'             => [
            'fields' => ['title'],
            'format' => '%s',
        ],
        'global_operations' => [
            'all' => [
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"',
            ],
        ],
        'operations'        => [
            'edit'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_exporter']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ],
            'copy'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_exporter']['copy'],
                'href'  => 'act=copy',
                'icon'  => 'copy.gif',
            ],
            'delete' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_exporter']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
            ],
            'show'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_exporter']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ],
        ],
    ],

    // Palettes
    'palettes'    => [
        '__selector__'                               => [
            'fileType',
            'addHeaderToExportTable',
            'overrideHeaderFieldLabels',
            'addJoinTables',
            'type',
            'target',
            'fileNameAddDatime',
        ],
        'default'                                    => '{title_legend},title,type;',
        \HeimrichHannot\Exporter\Exporter::TYPE_LIST => '{title_legend},title,type;' . '{export_legend},target,fileType;'
                                                        . '{table_legend},globalOperationKey,linkedTable,addJoinTables,addUnformattedFields,tableFieldsForExport,restrictToPids,whereClause,orderBy;',
        \HeimrichHannot\Exporter\Exporter::TYPE_ITEM => '{title_legend},title,type;' . '{export_legend},target,fileType;'
                                                        . '{table_legend},linkedTable,addJoinTables,skipFields,skipLabels,whereClause,orderBy;',
    ],

    // Subpalettes
    'subpalettes' => [
        'fileType_csv'                                                 => 'exporterClass,fieldDelimiter,fieldEnclosure,localizeFields,addHeaderToExportTable',
        'fileType_pdf'                                                 => 'exporterClass,pdfBackground,pdfFonts,pdfMargins,pdfTitle,pdfSubject,pdfCreator,localizeFields,pdfCss,pdfTemplate',
        'fileType_xls'                                                 => 'exporterClass,localizeFields,addHeaderToExportTable',
        'fileType_media'                                               => 'exporterClass,compressionType',
        'addHeaderToExportTable'                                       => 'localizeHeader,overrideHeaderFieldLabels',
        'overrideHeaderFieldLabels'                                    => 'headerFieldLabels',
        'addJoinTables'                                                => 'joinTables',
        'target_' . \HeimrichHannot\Exporter\Exporter::TARGET_DOWNLOAD => 'fileName,fileNameAddDatime',
        'target_' . \HeimrichHannot\Exporter\Exporter::TARGET_FILE     => 'fileDir,useHomeDir,fileSubDirName,fileName,fileNameAddDatime',
        'fileNameAddDatime'                                            => 'fileNameAddDatimeFormat',
    ],

    // Fields
    'fields'      => [
        'id'                        => [
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ],
        'tstamp'                    => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'title'                     => [
            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['title'],
            'exclude'   => true,
            'search'    => true,
            'sorting'   => true,
            'flag'      => 1,
            'inputType' => 'text',
            'eval'      => [
                'tl_class'  => 'w50',
                'mandatory' => true,
                'maxlength' => 255,
            ],
            'sql'       => "varchar(196) NOT NULL default ''",
        ],
        'type'                      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['type'],
            'inputType' => 'select',
            'options'   => [
                \HeimrichHannot\Exporter\Exporter::TYPE_LIST,
                \HeimrichHannot\Exporter\Exporter::TYPE_ITEM,
            ],
            'reference' => &$GLOBALS['TL_LANG']['tl_exporter']['reference'],
            'eval'      => [
                'mandatory'      => true,
                'tl_class'       => 'w50',
                'submitOnChange' => true,
            ],
            'sql'       => "varchar(16) NOT NULL default 'list'",
        ],

        // table legend
        'linkedTable'               => [
            'label'            => &$GLOBALS['TL_LANG']['tl_exporter']['linkedTable'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => ['HeimrichHannot\Exporter\Backend', 'getLinkedTablesAsOptions'],
            'eval'             => [
                'chosen'             => true,
                'mandatory'          => true,
                'submitOnChange'     => true,
                'includeBlankOption' => true,
                'tl_class'           => 'w50',
            ],
            'sql'              => "varchar(64) NOT NULL default ''",
        ],
        'globalOperationKey'        => [
            'label'            => &$GLOBALS['TL_LANG']['tl_exporter']['globalOperationKey'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => ['HeimrichHannot\Exporter\Backend', 'getGlobalOperationKeysAsOptions'],
            'eval'             => [
                'mandatory'          => true,
                'submitOnChange'     => true,
                'includeBlankOption' => true,
                'tl_class'           => 'w50',
            ],
            'sql'              => "varchar(32) NOT NULL default ''",
        ],
        'restrictToPids'            => [
            'label'            => &$GLOBALS['TL_LANG']['tl_exporter']['restrictToPids'],
            'exclude'          => true,
            'filter'           => true,
            'inputType'        => 'select',
            'options_callback' => ['\HeimrichHannot\Exporter\Backend', 'getTableArchives'],
            'eval'             => [
                'tl_class'           => 'long clr',
                'style'              => 'width: 97%',
                'chosen'             => true,
                'includeBlankOption' => true,
                'multiple'           => true,
            ],
            'sql'              => "blob NULL",
        ],
        'skipFields'                => [
            'label'            => &$GLOBALS['TL_LANG']['tl_exporter']['skipFields'],
            'exclude'          => true,
            'filter'           => true,
            'inputType'        => 'select',
            'options_callback' => ['HeimrichHannot\Exporter\Backend', 'getTableFields'],
            'eval'             => ['multiple' => true, 'chosen' => true, 'tl_class' => 'long', 'style' => 'width: 97%'],
            'sql'              => "blob NULL",
        ],
        'skipLabels'                => [
            'label'            => &$GLOBALS['TL_LANG']['tl_exporter']['skipLabels'],
            'exclude'          => true,
            'filter'           => true,
            'inputType'        => 'select',
            'options_callback' => ['HeimrichHannot\Exporter\Backend', 'getTableFields'],
            'eval'             => ['multiple' => true, 'chosen' => true, 'tl_class' => 'long', 'style' => 'width: 97%'],
            'sql'              => "blob NULL",
        ],
        'addUnformattedFields'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['addUnformattedFields'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => [
                'submitOnChange' => true,
                'tl_class'       => 'w50 clr',
            ],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'tableFieldsForExport'      => [
            'inputType'        => 'checkboxWizard',
            'label'            => &$GLOBALS['TL_LANG']['tl_exporter']['tableFieldsForExport'],
            'options_callback' => ['HeimrichHannot\Exporter\Backend', 'getTableFields'],
            'exclude'          => true,
            'eval'             => [
                'multiple'  => true,
                'tl_class'  => 'w50 autoheight clr',
                'mandatory' => true,
            ],
            'sql'              => "blob NULL",
        ],

        // export legend
        'fileType'                  => [
            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['fileType'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => [
                EXPORTER_FILE_TYPE_CSV,
                EXPORTER_FILE_TYPE_PDF,
                EXPORTER_FILE_TYPE_XLS,
                EXPORTER_FILE_TYPE_MEDIA,
            ],
            'reference' => &$GLOBALS['TL_LANG']['tl_exporter']['fileType'],
            'eval'      => [
                'mandatory'          => true,
                'includeBlankOption' => true,
                'submitOnChange'     => true,
                'tl_class'           => 'w50 clr',
            ],
            'sql'       => "varchar(10) NOT NULL default ''",
        ],
        'exporterClass'             => [
            'label'            => &$GLOBALS['TL_LANG']['tl_exporter']['exporterClass'],
            'inputType'        => 'select',
            'eval'             => ['mandatory' => true, 'tl_class' => 'w50', 'decodeEntities' => true],
            'options_callback' => ['HeimrichHannot\Exporter\Backend', 'getExporterClasses'],
            'sql'              => "varchar(255) NOT NULL default ''",
        ],
        'fieldDelimiter'            => [
            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['fieldDelimiter'],
            'exclude'   => true,
            'search'    => true,
            'sorting'   => true,
            'flag'      => 1,
            'inputType' => 'text',
            'default'   => ',',
            'eval'      => [
                'mandatory' => true,
                'maxlength' => 1,
                'tl_class'  => 'w50 clr',
            ],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'fieldEnclosure'            => [
            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['fieldEnclosure'],
            'exclude'   => true,
            'search'    => true,
            'sorting'   => true,
            'flag'      => 1,
            'inputType' => 'text',
            'default'   => '"',
            'eval'      => [
                'mandatory' => true,
                'maxlength' => 1,
                'tl_class'  => 'w50',
            ],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'addHeaderToExportTable'    => [
            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['addHeaderToExportTable'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => [
                'submitOnChange' => true,
                'tl_class'       => 'w50 clr',
            ],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'overrideHeaderFieldLabels' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['overrideHeaderFieldLabels'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => [
                'submitOnChange' => true,
                'tl_class'       => 'w50 clr',
            ],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'headerFieldLabels'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['headerFieldLabels'],
            'exclude'   => true,
            'inputType' => 'multiColumnEditor',
            'eval'      => [
                'tl_class'          => 'clr',
                'multiColumnEditor' => [
                    'minRowCount' => 0,
                    'fields'      => [
                        'field' => [
                            'label'            => &$GLOBALS['TL_LANG']['tl_exporter']['headerFieldLabels']['field'],
                            'options_callback' => ['HeimrichHannot\Exporter\Backend', 'getTableFields'],
                            'inputType'        => 'select',
                            'eval'             => ['chosen' => true, 'style' => 'width: 250px'],
                        ],
                        'label' => [
                            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['headerFieldLabels']['label'],
                            'inputType' => 'text',
                            'eval'      => ['style' => 'width: 250px'],
                        ],
                    ],
                ],
            ],
            'sql'       => "blob NULL",
        ],
        'compressionType'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['compressionType'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => ['zip'],
            'reference' => &$GLOBALS['TL_LANG']['tl_exporter']['compressionType'],
            'eval'      => [
                'mandatory' => true,
                'tl_class'  => 'w50',
            ],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'localizeHeader'            => [
            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['localizeHeader'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => [
                'tl_class' => 'w50',
            ],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'localizeFields'            => [
            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['localizeFields'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => [
                'tl_class' => 'w50 clr',
            ],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'target'                    => [
            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['target'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => [
                \HeimrichHannot\Exporter\Exporter::TARGET_DOWNLOAD,
                \HeimrichHannot\Exporter\Exporter::TARGET_FILE,
            ],
            'reference' => &$GLOBALS['TL_LANG']['tl_exporter']['reference'],
            'eval'      => [
                'submitOnChange'     => true,
                'mandatory'          => true,
                'includeBlankOption' => true,
                'tl_class'           => 'w50',
            ],
            'sql'       => "varchar(255) NOT NULL default '" . \HeimrichHannot\Exporter\Exporter::TARGET_DOWNLOAD . "'",
        ],
        'fileDir'                   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['fileDir'],
            'exclude'   => true,
            'inputType' => 'fileTree',
            'eval'      => ['fieldType' => 'radio', 'mandatory' => true, 'tl_class' => 'w50 clr'],
            'sql'       => "binary(16) NULL",
        ],
        'useHomeDir'                => [
            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['useHomeDir'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'fileSubDirName'            => [
            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['fileSubDirName'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'fileName'                  => [
            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['fileName'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 64, 'tl_class' => 'w50'],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'fileNameAddDatime'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['fileNameAddDatime'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['submitOnChange' => true, 'tl_class' => 'w50 clr'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'fileNameAddDatimeFormat'   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['fileNameAddDatimeFormat'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 32, 'tl_class' => 'w50'],
            'sql'       => "varchar(32) NOT NULL default ''",
        ],
        'addJoinTables'             => [
            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['addJoinTables'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['submitOnChange' => true, 'tl_class' => 'clr'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'joinTables'                => [
            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['joinTables'],
            'inputType' => 'multiColumnEditor',
            'eval'      => [
                'multiColumnEditor' => [
                    'fields' => [
                        'joinTable'     => [
                            'label'            => &$GLOBALS['TL_LANG']['tl_exporter']['joinTable'],
                            'inputType'        => 'select',
                            'options_callback' => ['HeimrichHannot\Exporter\Backend', 'getAllTablesAsOptions'],
                            'eval'             => [
                                'chosen'             => true,
                                'mandatory'          => true,
                                'includeBlankOption' => true,
                                'groupStyle'         => 'width: 250px',
                                'style'              => 'width: 250px',
                            ],
                        ],
                        'joinCondition' => [
                            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['joinCondition'],
                            'inputType' => 'text',
                            'eval'      => [
                                'class'          => 'long',
                                'decodeEntities' => true,
                                'mandatory'      => true,
                                'groupStyle'     => 'width: 400px',
                                'style'          => 'width: 400px',
                            ],
                        ],
                    ],
                ],
            ],
            'sql'       => "blob NULL",
        ],
        'whereClause'               => [
            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['whereClause'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['tl_class' => 'w50 clr', 'decodeEntities' => true],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'orderBy'                   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['orderBy'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['tl_class' => 'w50 clr', 'decodeEntities' => true],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'pdfBackground'             => [
            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['pdfBackground'],
            'inputType' => 'fileTree',
            'exclude'   => true,
            'eval'      => [
                'filesOnly'  => true,
                'extensions' => 'pdf',
                'fieldType'  => 'radio',
                'tl_class'   => 'w50',
            ],
            'sql'       => "binary(16) NULL",
        ],
        'pdfTemplate'               => [
            'label'            => &$GLOBALS['TL_LANG']['tl_exporter']['pdfTemplate'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => ['HeimrichHannot\Exporter\Backend', 'getPdfExporterTemplates'],
            'eval'             => [
                'tl_class'           => 'w50 clr',
                'includeBlankOption' => true,
            ],
            'sql'              => "varchar(128) NOT NULL default ''",
        ],
        'pdfCss'                    => [
            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['pdfCss'],
            'inputType' => 'fileTree',
            'exclude'   => true,
            'eval'      => [
                'filesOnly'  => true,
                'extensions' => 'css',
                'fieldType'  => 'checkbox',
                'tl_class'   => 'w50',
            ],
            'sql'       => "blob NULL",
        ],
        'pdfFonts'                  => [
            'label'        => &$GLOBALS['TL_LANG']['tl_exporter']['pdfFonts'],
            'exclude'      => true,
            'inputType'    => 'fieldpalette',
            'foreignKey'   => 'tl_fieldpalette.id',
            'relation'     => ['type' => 'hasMany', 'load' => 'eager'],
            'sql'          => "blob NULL",
            'eval'         => ['tl_class' => 'long clr'],
            'fieldpalette' => [
                'config'   => [
                    'hidePublished' => true,
                ],
                'list'     => [
                    'label' => [
                        'fields' => ['exporter_pdfFonts_fontName', 'exporter_pdfFonts_fontWeight'],
                        'format' => '%s -> %s',
                    ],
                ],
                'palettes' => [
                    'default' => 'exporter_pdfFonts_fontName,exporter_pdfFonts_fontWeight,exporter_pdfFonts_file',
                ],
                'fields'   => [
                    'exporter_pdfFonts_fontName'   => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['exporter_pdfFonts_fontName'],
                        'inputType' => 'text',
                        'eval'      => ['tl_class' => 'clr', 'mandatory' => true],
                        'sql'       => "varchar(255) NOT NULL default ''",
                    ],
                    'exporter_pdfFonts_fontWeight' => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['exporter_pdfFonts_fontWeight'],
                        'inputType' => 'select',
                        'options'   => ['R', 'B', 'I', 'BI'],
                        'reference' => &$GLOBALS['TL_LANG']['tl_exporter']['exporter_pdfFonts_fontWeightOptions'],
                        'eval'      => ['tl_class' => 'clr', 'mandatory' => true, 'includeBlankOption' => true],
                        'sql'       => "varchar(16) NOT NULL default ''",
                    ],
                    'exporter_pdfFonts_file'       => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['exporter_pdfFonts_file'],
                        'inputType' => 'fileTree',
                        'exclude'   => true,
                        'eval'      => [
                            'filesOnly'  => true,
                            'extensions' => 'ttf',
                            'fieldType'  => 'radio',
                            'mandatory'  => true,
                            'tl_class'   => 'w50',
                        ],
                        'sql'       => "blob NULL",
                    ],
                ],
            ],
        ],
        'pdfMargins'                => [
            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['pdfMargins'],
            'exclude'   => true,
            'inputType' => 'trbl',
            'options'   => ['pt', 'in', 'cm', 'mm'],
            'eval'      => ['includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql'       => "varchar(128) NOT NULL default ''",
        ],
        'pdfTitle'                  => [
            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['pdfTitle'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 64, 'tl_class' => 'w50 clr'],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'pdfSubject'                => [
            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['pdfSubject'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 128, 'tl_class' => 'w50'],
            'sql'       => "varchar(128) NOT NULL default ''",
        ],
        'pdfCreator'                => [
            'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['pdfCreator'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 64, 'tl_class' => 'w50'],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
    ],
];

$arrDca = &$GLOBALS['TL_DCA']['tl_exporter'];

if (in_array('protected_homedirs', \ModuleLoader::getActive()))
{
    $arrDca['subpalettes']['target_' . \HeimrichHannot\Exporter\Exporter::TARGET_FILE] = str_replace(
        'useHomeDir',
        'useHomeDir,useProtectedHomeDir',
        $arrDca['subpalettes']['target_' . \HeimrichHannot\Exporter\Exporter::TARGET_FILE]
    );

    $arrDca['fields']['useProtectedHomeDir'] = [
        'label'     => &$GLOBALS['TL_LANG']['tl_exporter']['useProtectedHomeDir'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default ''",
    ];
}

class tl_exporter extends \Backend
{
    /**
     * Check permissions to edit table tl_exporter
     */
    public function checkPermission()
    {
        if (\BackendUser::getInstance()->isAdmin)
        {
            return;
        }
    }
}