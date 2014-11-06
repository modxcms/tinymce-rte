<?php

class TinyMCERTEOnRichTextEditorInit extends TinyMCERTEPlugin {

    public function init(){
        $useEditor = $this->modx->getOption('use_editor', false);
        $whichEditor = $this->modx->getOption('which_editor', '');

        if ($useEditor && $whichEditor == 'TinyMCE RTE') return true;

        return false;
    }

    public function process() {
        $html = $this->initTinyMCE();

        $this->modx->event->output($html);
    }

    private function initTinyMCE() {
        $this->modx->regClientStartupScript($this->tinymcerte->getOption('jsUrl') . 'vendor/tinymce/tinymce.min.js');
        $this->modx->regClientStartupScript($this->tinymcerte->getOption('jsUrl') . 'mgr/tinymcerte.js');

        return '<script type="text/javascript">

            TinyMCERTE.editorConfig = {
            };

        </script>';
    }
}