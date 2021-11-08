<?php
/**
 * @package tinymcerte
 * @subpackage plugin
 */

namespace TinyMCERTE\Plugins\Events;

use TinyMCERTE\Plugins\Plugin;

/**
 * class OnRichTextEditorRegister
 */
class OnRichTextEditorRegister extends Plugin
{
    /**
     * {@inheritDoc}
     * @return void
     */
    public function process()
    {
        $this->modx->event->output('TinyMCE RTE');
    }
}