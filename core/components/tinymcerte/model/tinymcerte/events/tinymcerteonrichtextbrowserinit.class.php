<?php

class TinyMCERTEOnRichTextBrowserInit extends TinyMCERTEPlugin {

    public function init(){
        $useEditor = $this->modx->getOption('use_editor', false);
        $whichEditor = $this->modx->getOption('which_editor', '');

        if ($useEditor && $whichEditor == 'TinyMCE RTE') return true;

        return false;
    }

    public function process() {
        $this->modx->controller->addJavascript($this->tinymcerte->getOption('jsUrl') . 'mgr/extras/browser.js');
        $this->modx->event->output('TinyMCERTE.browserCallback');
    }

}