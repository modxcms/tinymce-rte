<?php

abstract class TinyMCERTEPlugin {
    /** @var modX $modx */
    protected $modx;
    /** @var TinyMCERTE $tinymcerte */
    protected $tinymcerte;
    /** @var array $scriptProperties */
    protected $scriptProperties;

    public function __construct($modx, &$scriptProperties) {
        $this->scriptProperties =& $scriptProperties;
        $this->modx = $modx;
        $this->tinymcerte = $this->modx->tinymcerte;
    }

    public function run() {
        $init = $this->init();
        if ($init !== true) {
            return;
        }

        $this->process();
    }

    public function init() {
        return true;
    }

    abstract public function process();
}