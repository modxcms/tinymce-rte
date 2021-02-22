# TinyMCE Rich Text Editor
[![LICENSE](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](./LICENSE) [![Crowdin](https://badges.crowdin.net/modx-tinymce-rte/localized.svg)](https://crowdin.com/project/modx-tinymce-rte)

TinyMCE is a platform independent web based Javascript HTML WYSIWYG editor. It
allows non-technical users to format content without knowing how to code. This
package is based on the TinyMCE 5.

To build and install the package from source you have to use [Git Package
Management](https://github.com/TheBoxer/Git-Package-Management). The GitHub
repository of TinyMCE Rich Text Editor contains a
[config.json](https://github.com/Jako/tinymce-rte/blob/tinymce5/_build/config.json)
to build that package locally. Use this option, if you want to debug TinyMCE
Rich Text Editor and/or contribute bugfixes and enhancements.

To update the TinyMCE core, you have to change the `tinymce_version` property in
the `gruntfile.js` and run the `grunt update` task afterwards. This task will
prepare all needed files in `assets/components/tinymcerte`. Please check, wether
the TinyMCE image and link plugins are changed in the new version of TinyMCE and
adopt the changes in the `src/modx/plugins` files and run the `grunt prepare`
task afterwards.

To modify the MODX skin, you have to open the [TinyMCE 5 Skin
Tool](http://skin.tiny.cloud/t5/) and upload the current
[skintool.json](https://github.com/Jako/tinymce-rte/blob/tinymce5/src/modx/skintool.json).
After modifying the skin, you must click on `Get Skin` and replace the skin
folder in the src/modx folder with the extracted downloaded skin folder. Then
you should run the `grunt update` task. This task will prepare all needed files
in `assets/components/tinymcerte`.

There is a `grunt prepare_debug` task, that copies not uglyfied script files to
`assets/components/tinymcerte`.

## Change Log

__2.0.0__
- Upgrade TinyMCE to 5
- Refactored modxlink TinyMCE plugin to use the nested link_list option
- Refactored modximage TinyMCE plugin
- Recursive merge the external config with the config
- Remove the deprecated file_browser_callback and use the file_picker_callback
- Allow direct JSON based style_formats items

__1.4.0__
- Build the modx skin with the internal tinymce grunt workflow
- Extend/Fix the modx skin styles
- Fix an issue with the table tool buttons

__1.3.4__
- The modx skin extends the lightgray skin, that way the css changes in the lightgray skin are available after a TinyMCE update
- Some lexicon changes/improvements
- Removed some unnecessary files
- Upgrade TinyMCE to 4.9.11

__1.3.3__
- Bugfix for not using full width when the editor is moved to a new tab [#86]
- Upgrade TinyMCE to 4.9.7

__1.3.2__
- Bugfix for showing only an english user interface

__1.3.1__
- Get settings from a JSON encoded array in tinymcerte.settings system setting
- Make the entity_encoding configurable [#79]

__1.3.0__
- Manage TinyMCE release download by npm
- Add Gruntfile.js that copies the current release of TinyMCE to the corresponding folders
- Add version info to the registered assets
- Upgrade TinyMCE to  4.8.3
- Adding Russian translation

__1.2.1__
- Escaped special HTML chars in the modxlink plugin
- Fixing 'Media browser does not close when clicking on close'
- Added language strings for the system settings added in 1.2.0

__1.2.0__
- Added `relative_urls` & `remove_script_host` settings
- Plugin now makes use of `modManagerController::addJavascript` instead of `modX::regClientStartupScript`
- Added system setting to define 'valid_elements'
- Added 'links_across_contexts' setting to limit links to the current context resources
- Added support for configured default Media Source in context settings
- CMPs can now pass any TinyMCE configuration property using the `OnRichTextEditorInit` system event
- Upgraded to TinyMCE 4.5.7

__1.1.1__
- Allow base path parsing in the external_config system setting
- Add tel: prefix
- Add modximage - left/right image positioning
- Sync tinymce and textarea
- Add modx skin (Credits goes to fourroses666)
- Add skin system setting

__1.1.0__
- Add autocomplete search for links
- Add external config
- Support for link classes

__1.0.0__
- Initial release.

