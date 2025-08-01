# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.1.2] - 2025-8-1

### Changed

- Add addititional translations
- Fix page search box disappearing on tab change in link window

## [3.1.1] - 2025-7-21

### Changed

- Add missing label when adding modxlink to the menu
- Fix issue with being unable to add a link when enable_link_list is set
- Respect manager language


## [3.1.0] - 2025-06-13

### Changed

- Add "rel" link field
- Add system setting "enable_link_aria" to show "aria_label". "aria_labelledby", "aria_describedby" and "id" in link field

## [3.0.5] - 2025-06-10

### Changed

- Fix issue when multiple links are on the same line

## [3.0.4] - 2025-06-06

### Changed

- Fix z-index issue with MIGX
- Fix typo on tinymcerte.skin system setting

## [3.0.3] - 2025-06-04

### Changed

- Automatically remove modximage from plugins if still installed

## [3.0.2] - 2025-06-03

### Changed

- Added cache busting

## [3.0.1] - 2025-05-29

### Changed

- Switch to local instance of TinyMCE 6

## [3.0.0] - 2025-05-29

### Changed

- Refactored the project to use TinyMCE 6

## [2.1.1] - 2025-03-21

### Changed

- Update to the newest modAI API

## [2.1.0] - 2025-02-28

### Added

- Add support for modAI

## [2.0.9] - 2022-09-28

### Fixed

- Fix `max_height` system setting not cast right

## [2.0.8] - 2022-08-01

### Added

- Add a default `max_height` system setting to restrict the height of the editor with an enabled `autoresize` plugin

### Fixed

- Fill the TinyMCE `document_base_url` with the `site_url` context setting instead of the `site_url` system setting [#121]

## [2.0.7] - 2022-03-18

### Fixed

- Avoid and log an invalid TinyMCE configuration
- Get the right manager language in MODX 3.x

## [2.0.6] - 2022-02-16

### Added

- Change `lightgray` skin system setting to `modx` during an update
- Add `autoresize` plugin per default

## [2.0.5] - 2021-12-27

### Added

- Unescape escaped regex strings (i.e. to allow javascript regex filters in an external config) [#117] 

### Fix

- Escape MySQL reserved word rank [#119]

## [2.0.4] - 2021-12-03

### Added

- Load the TinyMCE configuration even if the resource is not using a rich text editor
- Allow drop of MODX elements to the editor content in richtext template variables

### Fix

- Fix an uncaught type error when the current resource has richtext disabled and uses ContentBlocks

## [2.0.3] - 2021-10-01

### Fix

- Fix setting the link text after selecting a resource

## [2.0.2] - 2021-09-30

### Changed

- Update TinyMCE to 5.9.2
- Restored compatibility for PHP 7.1 and less

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
