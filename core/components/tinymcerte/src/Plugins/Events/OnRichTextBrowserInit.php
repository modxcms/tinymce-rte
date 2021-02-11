<?php
/**
 * @package tinymcerte
 * @subpackage plugin
 */

namespace TinyMCERTE\Plugins\Events;

use TinyMCERTE\Plugins\Plugin;

class OnRichTextBrowserInit extends Plugin
{
    public function init(): bool
    {
        $useEditor = $this->modx->getOption('use_editor', false);
        $whichEditor = $this->modx->getOption('which_editor', null, '');

        if ($useEditor && $whichEditor == 'TinyMCE RTE') {
            return true;
        }

        return false;
    }

    public function process()
    {
        $this->modx->controller->addJavascript($this->tinymcerte->getOption('jsUrl') . 'mgr/browser.min.js?v=' . $this->tinymcerte->version);
        $this->modx->event->output('TinyMCERTE.browserCallback');
    }
}
