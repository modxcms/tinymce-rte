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
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     * @return mixed|void
     */
    public function process()
    {
        $this->modx->controller->addLexiconTopic('tinymcerte:default');
    }
}
