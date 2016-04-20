<?php

/**
 * TinyMCE Rich Tech Editor integration FontAwesome plugin
 *
 * This plugin register the font-awesome css in the manager
 *
 * original plugin credit https://github.com/josh18/TinyMCE-FontAwesome-Plugin
 *
 */


switch ($modx->event->name) {
    case 'OnManagerPageBeforeRender':
        $cssurl = $modx->getOption('tinymcerte.content_css', null, "//netdna.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css");
        $modx->regClientCSS($cssurl);
        break;
}
