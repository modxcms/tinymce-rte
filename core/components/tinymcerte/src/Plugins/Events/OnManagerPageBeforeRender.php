<?php
/**
 * @package tinymcerte
 * @subpackage plugin
 */

namespace TinyMCERTE\Plugins\Events;

use TinyMCERTE\Plugins\Plugin;

/**
 * Class OnManagerPageBeforeRender
 */
class OnManagerPageBeforeRender extends Plugin
{
    /**
     * {@inheritDoc}
     * @return bool
     */
    public function init()
    {
        $useEditor = $this->modx->getOption('use_editor', false);
        $whichEditor = $this->modx->getOption('which_editor', null, '');

        if ($useEditor && $whichEditor == 'TinyMCE RTE') {
            // Load the tinymcerte lexicon, when the editor is TinyMCE RTE
            return parent::init();
        }

        return false;
    }

    /**
     * {@inheritDoc}
     * @return void
     */
    public function process()
    {
        $this->modx->controller->addLexiconTopic('tinymcerte:default');
    }
}
