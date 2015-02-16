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
        $language = $this->modx->getOption('manager_language');
        $language = $this->tinymcerte->getLanguageCode($language);

        return array(
            'plugins' => $this->tinymcerte->getOption('plugins', array(), 'advlist autolink lists link image charmap print preview anchor visualblocks searchreplace code fullscreen insertdatetime media table contextmenu paste'),
            'toolbar1' => $this->tinymcerte->getOption('toolbar1', array(), 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image'),
            'toolbar2' => $this->tinymcerte->getOption('toolbar2', array(), ''),
            'toolbar3' => $this->tinymcerte->getOption('toolbar3', array(), ''),
            'language' => $language,
            'directionality' => $this->modx->getOption('manager_direction', array(), 'ltr'),
            'menubar' => $this->tinymcerte->getOption('menubar', array(), 'file edit insert view format table tools'),
            'statusbar' => $this->tinymcerte->getOption('statusbar', array(), 1) == 1,
            'image_advtab' => $this->tinymcerte->getOption('image_advtab', array(), true),
        );
    }


}
