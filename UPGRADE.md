# UPGRADE GUIDE

## From Exporter Module

### config.php

Replace `\HeimrichHannot\Exporter\ModuleExporter::getBackendModule()` with  `['huh.exporter.action.backendexport', 'export']`

### DCA 

Replace `\HeimrichHannot\Exporter\ModuleExporter::getGlobalOperation` with `\HeimrichHannot\ContaoExporterBundle\Action\BackendExportAction::getGlobalOperation`.

### Hooks & Events

All hooks were replaced with Symfony Events. 

The hooks `exporter_modifyFileDir` and `exporter_modifyFilename` were replaced with the `huh.exporter.event.before_export` event.

The hook `exporter_modifyFieldValue` was replaced with `huh.exporter.event.modifyfieldvalue` event.

The hook `exporter_modifyHeaderFields` was replaced with `huh.exporter.event.modifyheaderfields` event.