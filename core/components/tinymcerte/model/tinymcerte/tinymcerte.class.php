<?php

/**
 * The main TinyMCE Rich Text Editor service class.
 *
 * @package tinymcerte
 */
class TinyMCERTE {
    public $modx = null;
    public $namespace = 'tinymcerte';
    public $version = '1.3.1';
    public $cache = null;
    public $options = array();

    public function __construct(modX &$modx, array $options = array()) {
        $this->modx =& $modx;
        $this->namespace = $this->getOption('namespace', $options, 'tinymcerte');

        $corePath = $this->getOption('core_path', $options, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/tinymcerte/');
        $assetsPath = $this->getOption('assets_path', $options, $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/tinymcerte/');
        $assetsUrl = $this->getOption('assets_url', $options, $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/tinymcerte/');

        /* loads some default paths for easier management */
        $this->options = array_merge(array(
            'namespace' => $this->namespace,
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'templatesPath' => $corePath . 'templates/',
            'assetsPath' => $assetsPath,
            'assetsUrl' => $assetsUrl,
            'jsUrl' => $assetsUrl . 'js/',
            'cssUrl' => $assetsUrl . 'css/',
            'connectorUrl' => $assetsUrl . 'connector.php'
        ), $options);

        $this->modx->lexicon->load('tinymcerte:default');
    }

    /**
     * Get a local configuration option or a namespaced system setting by key.
     *
     * @param string $key The option key to search for.
     * @param array $options An array of options that override local options.
     * @param mixed $default The default value returned if the option is not found locally or as a
     * namespaced system setting; by default this value is null.
     * @return mixed The option value or the default value specified.
     */
    public function getOption($key, $options = array(), $default = null) {
        $option = $default;
        if (!empty($key) && is_string($key)) {
            if ($options != null && array_key_exists($key, $options)) {
                $option = $options[$key];
            } elseif (array_key_exists($key, $this->options)) {
                $option = $this->options[$key];
            } elseif (array_key_exists("{$this->namespace}.{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}.{$key}");
            }
        }
        return $option;
    }

    public function getLanguageCode($language) {
        $codes = array(
            'ar' => 'ar_EG',
            'be' => 'be_BY',
            'bg' => 'bg_BG',
            'cs' => 'cs_CZ',
            'da' => 'da_DK',
            'de' => 'de_DE',
            'el' => 'el_GR',
            'es' => 'es_ES',
            'et' => 'et_EE',
            'fa' => 'fa_IR',
            'fi' => 'fi_FI',
            'fr_FR' => 'fr_FR',
            'he_IL' => 'he_IL',
            'id' => 'id_ID',
            'it' => 'it_IT',
            'ja' => 'ja_JP',
            'nl' => 'nl_NL',
            'pl' => 'pl_PL',
            'pt_BR' => 'pt_BR',
            'ro' => 'ro_RO',
            'ru' => 'ru_RU',
            'sk' => 'sk_SK',
            'sv_SE' => 'sv_SE',
            'th_TH' => 'th_TH',
            'uk' => 'uk_UA',
            'zh_CN' => 'zh_CN',
        );

        if (isset($codes[$language])) {
            $language = $codes[$language];
        }

        $langFile = $this->getOption('assetsPath') . 'js/vendor/tinymce/langs/' . $language . '.js';
        if (!file_exists(($langFile))) {
            return 'en';
        }

        return $language;
    }
    
    public function explodeAndClean($array, $delimiter = ',') {
        $array = explode($delimiter, $array);     // Explode fields to array
        $array = array_map('trim', $array);       // Trim array's values
        $array = array_keys(array_flip($array));  // Remove duplicate fields
        $array = array_filter($array);            // Remove empty values from array
        return $array;
    }
}
