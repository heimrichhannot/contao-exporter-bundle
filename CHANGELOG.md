# Changelog
All notable changes to this project will be documented in this file.

## [0.4.2] - 2018-09-07

#### Fixed
- localization for fields from joined table
- `getTableArchives` to get archives as options in exporter config

## [0.4.1] - 2018-09-06

#### Fixed
- output path for file export

## [0.4.0] - 2018-09-06

#### Changed
- replaced spreadsheet lib to box/spout
- moved exporter to system backend group
- xls -> xlsx conversion is now mandatory in migration command

#### Removed
- createHeaders -> not necessary anymore

## [0.3.0] - 2018-07-27

#### Added
- more Exception classes
- Backend: better error messages
- Backend: redirect to calling route if error
- enhanced english translations

#### Changed
- made BackendExportAction::getGlobalOperation() non static and to be called from service
- added AbstractExporter::finishExport() to be able to overwrite export target
- changed exporter dca to class based settings
- changed DataContainer callback class names
- code enhancements

## [0.2.0] - 2018-07-26

#### Added 
- database migration command
- you can now also just pass a list of field names to exporter `$fields` argument

#### Changed
- ExportTypeItemInterface::prepareItemFields arguments order

## [0.1.0] - 2018-07-25

Initial release.
