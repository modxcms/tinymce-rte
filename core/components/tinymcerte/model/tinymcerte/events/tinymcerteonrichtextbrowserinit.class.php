<?php
/**
 * @package tinymcerte
 * @subpackage plugin
 */

class TinyMCERTEOnRichTextBrowserInit extends TinyMCERTEPlugin
{
    public function init()
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
        $this->modx->controller->addJavascript($this->tinymcerte->getOption('jsUrl') . 'mgr/browser.js?v=' . $this->tinymcerte->version);
        $this->modx->event->output('TinyMCERTE.browserCallback');
    }
}
