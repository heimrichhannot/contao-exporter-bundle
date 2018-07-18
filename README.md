# Contao Exporter Bundle 

> This bundle is currently in development and not ready for usage!

A backend module for exporting any contao entity to file.

![alt myModulePreview](docs/img/screenshot.png)

*Export config preview*

## Features

- export an entity list in the backend
- export of entities in the frontend
- currently supported file types:
    - csv
    - xls
    - pdf
    - zip (media file export as zip)

### Classes

Name | Description
---- | -----------
CsvExporter | An exporter for writing entity instances into a CSV file
XlsExporter | An exporter for writing entity instances into an excel file (XLS)
MediaExporter | An exporter that combines all files referenced by the selected properties of an entity in one archive file (e.g. zip) preserving the folder structure
PdfExporter | An exporter for creating a pdf out of an entity

### Hooks

Name | Arguments | Expected return value | Description
---- | --------- | --------------------- | -----------
exporter_modifyMediaFilename | $objFile, $strFieldname, $varFieldValue, $objMediaExporter | $objFile->path | Modify a filename just before added to the archive when using *MediaExporter* (also folder structure could be modified here)

## Technical instruction

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

You can use [frontendedit](https://github.com/heimrichhannot/contao-frontendedit) or [formhybrid_list](https://github.com/heimrichhannot/contao-formhybrid_list) in order to easily create a module for manipulating your entities in the frontend. It already contains a function to export entities after submission!

### Step 1
Create a configuration for your export by using the exporter's backend module (group devtools).

### Step 2
Call `export()` of `huh.exporter.action.export` service in your module:

```php
$container->get('huh.exporter.action.export')->export($config, $entity, $fields);
```

If you add ```$fields```, this array will be iteratd automatically in your template. Alternatively you can print every entity's property using $this in the template.

## Developers

### Events

Eventname                 | Event-ID                              | Description
--------------------------|---------------------------------------|------------
Before Export             | huh.exporter.event.before_export      | Fired before start of export. Customize file name and file path.
Before Build Query        | huh.exporter.event.before_build_query | Fired before building and executing the query for collecting list content. 
Modify Table Header field | huh.exporter.event.modifyheaderfields | Modify header field values in tables.
Modify Table field value  | huh.exporter.event.modifyfieldvalue   | Fired before writing a table value to the table object (e.g. spreadsheet).
