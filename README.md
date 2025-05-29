# TinyMCE Rich Text Editor

TinyMCE 6 for MODX Revolution

## Features

TinyMCE is a platform independent web based Javascript HTML WYSIWYG editor. It
allows non-technical users to format content without knowing how to code. This
package is based on the TinyMCE 6.

To build and install the package from source you have to use [Git Package
Management (GPM)](https://github.com/TheBoxer/Git-Package-Management). The GitHub
repository of TinyMCE Rich Text Editor contains a
[config.json](https://github.com/modxcms/tinymce-rte/blob/main/_build/config.json)
to build that package locally. Use this option, if you want to debug TinyMCE
Rich Text Editor and/or contribute bugfixes and enhancements.

To update the plugin run `npm install` to install the dependencies. Run 
`npm run build` to build the plugin. The source files are located in the `/src/`
directory. The package is compiled using Node 20.

To update the TinyMCE core, you have to change the `tinymcerte.tiny_url` system setting.

## Change Log
https://github.com/modxcms/tinymce-rte/blob/master/core/components/tinymcerte/docs/changelog.md

