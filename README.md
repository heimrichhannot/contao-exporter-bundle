# Contao Exporter Bundle 

A module for exporting any contao entity to file.

![Contao Exporter Bundle Backend Config Preview](docs/img/screenshot.png)

*Export config preview*

## Features

- export entities and list of entities
- easily add backend modules to your application/extension or use the frontendmodule
- expandable exporter architecture
- included exporter:
    - csv
    - Excel (xlsx, xls)
    - pdf
    - Media files (export media files assoziated with an entity as archive(zip))
    
Csv and Excel export are archived by [PhpSpreadsheet library](https://github.com/PHPOffice/PhpSpreadsheet). 
PDF export is archived by [mPDF library](https://github.com/mpdf/mpdf). This library comes not as dependency and therefore must be added to your bundle/project dependencies to archvie pdf export functionality.

## Technical instruction

### Install 

Install with composer:

```
composer require heimrichhannot/contao-exporter-bundle
```

If you want to use the pdf exporter, add `"mpdf/mpdf":"^7.0"` to your composer dependencies.

### Backend export

### Step 1
Define your global operation in your entity's dca as follows:

```php
'global_operations' => array
(
    'export_csv' => HeimrichHannot\ContaoExporterBundle\Action\BackendExportAction::getGlobalOperation('export_csv',
                 $GLOBALS['TL_LANG']['MSC']['export_csv'],
                 'system/modules/exporter/assets/img/icon_export.png')
),
```

### Step 2
Add your backend module in your entity's config.php as follows:

```php
$GLOBALS['BE_MOD']['mygroup'] = [
    'export_csv' => ['huh.exporter.action.backendexport', 'export']
]
```

### Step 3
Create a configuration for your export by using the exporter's backend module (group devtools).

## Frontend
You can use the included frontend module to add an easy export functionality. 

You can also use [frontendedit](https://github.com/heimrichhannot/contao-frontendedit) or [formhybrid_list](https://github.com/heimrichhannot/contao-formhybrid_list) in order to easily create a module for manipulating your entities in the frontend. It already contains a function to export entities after submission!

You can also create an custom implementation for your extension:

1) Create a configuration for your export by using the exporter's backend module (group devtools).
2) Call `export()` of `huh.exporter.action.export` service in your module:

```php
$container->get('huh.exporter.action.export')->export($config: ExporterModel, $entity: int|string, $fields = []: array);
```

## Developers

### Upgrade from exporter module

Please see [Upgrade Instructions](UPGRADE.md).

### Templates

You can overwrite the pdf output template. Templates are written in Twig and name should start with `exporter_pdf_`. See `exporter_pdf_item_default.html.twig` for a working example.

### Events

Eventname                 | Event-ID                              | Description
--------------------------|---------------------------------------|------------
Before Export             | huh.exporter.event.before_export      | Fired before start of export. Customize file name and file path.
Before Build Query        | huh.exporter.event.before_build_query | Fired before building and executing the query for collecting list content. 
Modify Table Header field | huh.exporter.event.modifyheaderfields | Modify header field values in tables.
Modify Table field value  | huh.exporter.event.modifyfieldvalue   | Fired before writing a table value to the table object (e.g. spreadsheet).
Modify Media File Name    | huh.exporter.event.modifymediafilename| Modify media file before adding to archive (filename and file object). 

### Add custom exporter

You can add custom exporter to add additional file types or functionality. 

Your exporter class must implement `ExporterInterface` and must be registered in the container with the `huh_exporter.exporter` service tag. We recommend to extend `AbstractExporter`, because it already has most of the mechanics implemented. 