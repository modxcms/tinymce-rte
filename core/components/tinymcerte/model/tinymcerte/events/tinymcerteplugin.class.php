<?php
/**
 * @package tinymcerte
 * @subpackage plugin
 */

abstract class TinyMCERTEPlugin
{
    /** @var modX $modx */
    protected $modx;
    /** @var TinyMCERTE $tinymcerte */
    protected $tinymcerte;
    /** @var array $scriptProperties */
    protected $scriptProperties;

    public function __construct($modx, &$scriptProperties)
    {
        $this->scriptProperties = &$scriptProperties;
        $this->modx = &$modx;
        $corePath = $this->modx->getOption('tinymcerte.core_path', null, $this->modx->getOption('core_path') . 'components/tinymcerte/');
        $this->tinymcerte = $this->modx->getService('tinymcerte', 'TinyMCERTE', $corePath . 'model/tinymcerte/', [
            'core_path' => $corePath
        ]);
    }

    public function run()
    {
        $init = $this->init();
        if ($init !== true) {
            return;
        }

        $this->process();
    }

    public function init()
    {
        return true;
    }

    abstract public function process();
}