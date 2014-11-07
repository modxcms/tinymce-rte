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
            TinyMCERTE.editorConfig = ' . $this->modx->toJSON($this->getTinyConfig()) . ';

            Ext.onReady(function(){
                TinyMCERTE.loadForTVs();
            });
        </script>';
    }

    private function getTinyConfig() {
        return array(
            'plugins' => $this->tinymcerte->getOption('plugins', array(), ''),
            'toolbar1' => $this->tinymcerte->getOption('toolbar1', array(), ''),
            'toolbar2' => $this->tinymcerte->getOption('toolbar2', array(), ''),
            'toolbar3' => $this->tinymcerte->getOption('toolbar3', array(), ''),
        );
    }


}