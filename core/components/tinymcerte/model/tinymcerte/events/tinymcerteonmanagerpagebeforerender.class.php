<?php
/**
 * @package tinymcerte
 * @subpackage plugin
 */

class TinyMCERTEOnManagerPageBeforeRender extends TinyMCERTEPlugin
{
    public function init()
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
