# TinyMCE Rich Text Editor
[![LICENSE](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](./LICENSE)

### TinyMCE Rich Text Editor
TinyMCE is a platform independent web based Javascript HTML WYSIWYG editor. It allows non-technical users to format content without knowing how to code. This package is based on the TinyMCE 4. 



## Change Log

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

