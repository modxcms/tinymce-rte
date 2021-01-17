<?php
/**
 * @package tinymcerte
 * @subpackage plugin
 */

class TinyMCERTEOnRichTextEditorRegister extends TinyMCERTEPlugin
{
    public function process()
    {
        $this->modx->event->output('TinyMCE RTE');
    }
}