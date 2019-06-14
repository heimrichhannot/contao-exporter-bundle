# Changelog
All notable changes to this project will be documented in this file.

## [0.7.1] - 2019-06-14

* [FIXED] handle "Cannot perform I/O operation outside of the base folder:" and set temp dir to system/tmp in order to handle get_temp_dir() permission restrictions 

## [0.7.0] - 2019-05-07

This release fixes a serious bug with symfony 4, which handles class caching different that symfony 3. If you used this bundle with symfony 4, please be aware that exporter class names may be saved wrong. To resolve this, you need to save every exporter configuration with the correct exporter class again.

#### Changed
* updated dependencies
* updated translations
* removed file type exporter mapping caching 

#### Fixed
* bug with symfony 4 class caching
* class names instead of class aliases were used in migration command
* removed a deprecation warning
* removed a unused method

## [0.6.2] - 2019-02-14

#### Fixed
- missing `clr` style class

## [0.6.1] - 2019-01-23

#### Added
- language support for command export

## [0.6.0] - 2019-01-22

#### Added
- php cs fixer
- command for exporting on the command line
- field_value_copier for copying selected fields from one exporter config to another

## [0.5.2] - 2018-12-12

#### Fixed
- palette handling

## [0.5.1] - 2018-12-12

#### Fixed
- some services not marked public
- non existing exporter service

## [0.5.0] - 2018-10-17

#### Changed
- declaration of subpalettes for each exporterClass in correct syntax (removed slashes by underscore)

## [0.4.3] - 2018-09-11

#### Added
- optional ignore of onload_callbacks of the exported entities

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
