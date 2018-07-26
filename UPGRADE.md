# UPGRADE GUIDE

## Module to 1.0

### Database
First you must call the contao database upgrade tool due changed database fields

Afterward you should call the migration command `huh:exporter:migration`, it updates the exporterClass fields, changes the formhybrid type and gives the option to change file type from xls to xlsx.

### Export types

Custom export types like formhybrid are no longer supported. Formhybrid type is addressed in the migration command.

### config.php

Replace `\HeimrichHannot\Exporter\ModuleExporter::getBackendModule()` with  `['huh.exporter.action.backendexport', 'export']`

### DCA 

Replace `\HeimrichHannot\Exporter\ModuleExporter::getGlobalOperation` with `\HeimrichHannot\ContaoExporterBundle\Action\BackendExportAction::getGlobalOperation`.

### Hooks & Events

All hooks were replaced with Symfony Events. 

The hooks `exporter_modifyFileDir` and `exporter_modifyFilename` were replaced with the `huh.exporter.event.before_export` event.

The hook `exporter_modifyFieldValue` was replaced with `huh.exporter.event.modifyfieldvalue` event.

The hook `exporter_modifyHeaderFields` was replaced with `huh.exporter.event.modifyheaderfields` event.

The hook `exporter_modifyMediaFilename` was replaced with `huh.exporter.event.modifymediafile` event.

### PDF Export

The pdf export was migrated to [mPDF 7][1] and [Contao Utils Bundle][2] PDF Writer. Also the template handling is changed.

#### Fonts 

Instead of set the font configuration in the exporter config, there is now a field to select font folders. These folders must container a `mpdf-config.php` file. Please check the corresponding chapters in [Utils Bundle docs][3] and the [mPDF Docs][4].

#### Templates

We moved from the contao template engine to twig. Templates should be stored withing `src/Resources/view` folder and file name should end `.html.twig`. Following variables are available: `raw` (all fields), `fields` (all fields filtered by skipped fields and skipped labels), `skipFields` and `skipLabels`


[1]: https://mpdf.github.io
[2]: https://github.com/heimrichhannot/contao-utils-bundle
[3]: https://github.com/heimrichhannot/contao-utils-bundle/blob/master/docs/utils/pdf/pdf_writer.md#use-custom-fonts
[4]: https://mpdf.github.io/fonts-languages/fonts-in-mpdf-7-x.html