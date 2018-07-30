# Changelog
All notable changes to this project will be documented in this file.

## [0.3.0] - 2018-07-27

#### Added
* more Exception classes
* better backend error messages
* redirect to calling route if error
* enhanced english translations

#### Changed
* made BackendExportAction::getGlobalOperation() non static and to be called from service
* changed exporter dca to class based settings
* code enhancements

## [0.2.0] - 2018-07-26

#### Added 
* database migration command
* you can now also just pass a list of field names to exporter `$fields` argument

#### Changed
* ExportTypeItemInterface::prepareItemFields arguments order

## [0.1.0] - 2018-07-25

Initial release.