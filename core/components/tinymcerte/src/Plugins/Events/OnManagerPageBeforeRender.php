<?php
/**
 * @package tinymcerte
 * @subpackage plugin
 */

namespace TinyMCERTE\Plugins\Events;

use TinyMCERTE\Plugins\Plugin;

class OnManagerPageBeforeRender extends Plugin
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
        $this->modx->controller->addLexiconTopic('tinymcerte:default');
    }
}
