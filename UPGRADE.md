# UPGRADE GUIDE

## From Exporter Module

### Hooks & Events

The hooks `exporter_modifyFileDir` and `exporter_modifyFilename` were replaced with the `huh.exporter.event.before_export` event.

The hook `exporter_modifyFieldValue` was replaced with `huh.exporter.event.modifyfieldvalue` event.