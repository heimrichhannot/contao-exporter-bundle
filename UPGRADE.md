# UPGRADE GUIDE

## From Exporter Module

### Hooks & Event

The hooks `exporter_modifyFileDir` and `exporter_modifyFilename` were replaced with the `huh.exporter.event.before_export` event.