# TinyMCE Rich Text Editor

TinyMCE 5 for MODX Revolution

## Features

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
https://github.com/modxcms/tinymce-rte/blob/master/core/components/tinymcerte/docs/changelog.md

