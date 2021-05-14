# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.1] - 2021-05-14

### Changed

- Update TinyMCE to 5.8.0
- Improve the configuration output in the manager html code

### Fixed

- Compatibility with moregallery and Collections

## [2.0.0] - 2021-03-19

### Added

- MODX skintool.json for http://skin.tiny.cloud/t5/
- MODX 3 compatibility
- link_list_enable system setting

### Changed

- Upgrade TinyMCE to 5
- Refactored modxlink TinyMCE plugin to use the nested link_list option
- Refactored modximage TinyMCE plugin
- Recursive merge the external config with the config
- Remove the deprecated file_browser_callback and use the file_picker_callback 
- Allow direct JSON based style_formats items

## [1.4.0] - 2020-09-11

### Added

- Build the modx skin with the internal tinymce grunt workflow

### Changed

- Extend/Fix the modx skin styles
- Fix an issue with the table tool buttons

## [1.3.4] - 2020-08-12

### Added

- The modx skin extends the lightgray skin, that way the css changes in the lightgray skin are available after a TinyMCE update

### Changed

- Some lexicon changes/improvements
- Upgrade TinyMCE to 4.9.11

### Removed

- Removed some unnecessary files

## [1.3.3] - 2020-02-04

### Changed

- Bugfix for not using full width when the editor is moved to a new tab [#86]
- Upgrade TinyMCE to 4.9.7

## [1.3.2] - 2019-06-13

### Changed

- Bugfix for showing only an english user interface

## [1.3.1] - 2019-06-05

### Added

- Added field displaying resource pagetitle of MODX link [#83]
- Added image_caption option for TinyMCE [#60]

### Changed

- Expanding the locale list [#82]
- Get settings from a JSON encoded array in tinymcerte.settings system setting
- Make the entity_encoding configurable [#79]
- Upgrade TinyMCE to 4.9.4

## [1.3.0] - 2019-05-22

### Added

- Manage TinyMCE release download by npm
- Add Gruntfile.js that copies the current release of TinyMCE to the corresponding folders
- Add version info to the registered assets
- Adding Russian translation

### Changed

- Upgrade TinyMCE to 4.8.3

## [1.2.1] - 2017-12-16

### Added

- Added language strings for the system settings added in 1.2.0

### Changed

- Escaped special HTML chars in the modxlink plugin
- Fixing 'Media browser does not close when clicking on close'

## [1.2.0] - 2017-05-21

### Added

- Added `relative_urls` & `remove_script_host` settings
- Added system setting to define 'valid_elements'
- Added 'links_across_contexts' setting to limit links to the current context resources
- Added support for configured default Media Source in context settings
- CMPs can now pass any TinyMCE configuration property using the `OnRichTextEditorInit` system event

### Changed

- Plugin now makes use of `modManagerController::addJavascript` instead of `modX::regClientStartupScript`
- Upgraded to TinyMCE 4.5.7

## [1.1.1] - 2016-01-20

### Added

- Add tel: prefix
- Add modximage - left/right image positioning
- Add modx skin (Credits goes to fourroses666)
- Add skin system setting

### Changed

- Allow base path parsing in the external_config system setting
- Sync tinymce and textarea

## [1.1.0] - 2015-07-13

### Added

- Add autocomplete search for links
- Add external config
- Support for link classes

## [1.0.0] - 2015-02-23

### Added

- Initial release
