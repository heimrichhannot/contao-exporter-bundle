<?php

$arrLang = &$GLOBALS['TL_LANG']['tl_exporter'];

/**
 * Fields
 */
$arrLang['title'] = ['Title', 'Please type in the title for the exporter config.'];

// table legend
$arrLang['globalOperationKey'] = ['Global operation', 'Choose the operation the exporter should invoke.'];
$arrLang['linkedTable'] = ['Linked table', 'Choose the table, that should be exported.'];
$arrLang['tableFieldsForExport'] = ['Fields', 'Choose the fields to be exported.'];
$arrLang['localizeFields'] = ['Localize field values', 'Choose this option if field values should be localized.'];
$arrLang['addJoinTables'] = ['Add join', 'Choose this option if the linked table should be joined with some other table.'];
$arrLang['joinTables'] = ['Join elements', ''];
$arrLang['joinTable'] = ['Join table', 'Choose the table to be joined with the linked table.'];
$arrLang['joinCondition'] = ['ON condition', 'Please type in conditions for the ON clause in the form "linked_table.field = jin_table.field".'];
$arrLang['addUnformattedFields'] = ['Use unformatted fields', 'Choose this option if you want to have unformatted fields in your export.'];
$arrLang['whereClause'] = ['WHERE condition', 'Please type in a WHERE condition in the form column=X. In case of join the input needs to be extended in the form table.column=X. Temporal conditions need to be defined as timestamps.'];
$arrLang['orderBy'] = ['ORDER BY condition', 'Please type in a condition for ordering the exported data (e.g. tstamp ASC).'];

// export legend
$arrLang['fileType'] = ['File type', 'Choose the file type to be used for exporting.'];
$arrLang['fileType'][EXPORTER_FILE_TYPE_CSV] = 'CSV (comma separated values)';
$arrLang['fileType'][EXPORTER_FILE_TYPE_MEDIA] = 'Linked files as archive';
$arrLang['fileType'][EXPORTER_FILE_TYPE_PDF] = 'PDF';
$arrLang['fileType'][EXPORTER_FILE_TYPE_XLS] = 'XLS (Microsoft Excel)';
$arrLang['target'] = ['Target', 'Choose the file type to be exported to.'];
$arrLang['target'][EXPORTER_TARGET_DOWNLOAD] = 'Download';
$arrLang['fieldDelimiter'] = ['Field separator', 'Please type in the character separating fields.'];
$arrLang['fieldEnclosure'] = ['Text separator', 'Please type in the character enclosing texts containing the field separator character.'];
$arrLang['compressionType'] = ['Compression', 'Choose the format to use for compression of the archive.'];
$arrLang['compressionType']['zip'] = 'ZIP';
$arrLang['pdfBackground'] = ['Background', 'Choose a pdf template as a graphical base.'];
$arrLang['pdfTemplate'] = ['Template', 'Choose ein Template for the pdf.'];


// header
$arrLang['addHeaderToExportTable'] = ['Export field names in table header', 'Choose this option if you want to have the field names in the table\'s header.'];
$arrLang['localizeHeader'] = ['Localize table header', 'Choose this option if you want to localize the table header.'];
$arrLang['overrideHeaderFieldLabels'] = ['Override table header fields', 'Choose this option if you want to override some table header fields with custom labels.'];
$arrLang['headerFieldLabels'] = ['Table header fields', 'Type in the desired changes here.'];
$arrLang['headerFieldLabels']['field'] = 'Field';
$arrLang['headerFieldLabels']['label'] = 'Name';


/**
 * Legends
 */
$arrLang['title_legend'] = 'General settings';
$arrLang['export_legend'] = 'Exporter settings';
$arrLang['table_legend'] = 'Operations, tables & fields';


/**
 * Buttons
 */
$arrLang['new'] = ['New Exporter Configuration', 'Create Exporter Configuration'];
$arrLang['show'] = ['Exporter Configuration Details', 'Show Exporter Configuration ID %s details'];
$arrLang['edit'] = ['Edit Exporter Configuration', 'Edit Exporter Configuration ID %s'];
$arrLang['copy'] = ['Copy Exporter Configuration', 'Copy Exporter Configuration ID %s'];
$arrLang['delete'] = ['Delete Exporter Configuration', 'Delete Exporter Configuration ID %s'];