<?php
/**
 * @package tinymcerte
 * @subpackage plugin
 */

namespace TinyMCERTE\Plugins\Events;

use TinyMCERTE\Plugins\Plugin;

class OnRichTextEditorRegister extends Plugin
{
    public function process()
    {
        $this->modx->event->output('TinyMCE RTE');
    }
}