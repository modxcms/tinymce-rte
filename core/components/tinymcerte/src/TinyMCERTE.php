<?php
/**
 * TinyMCE Rich Text Editor
 *
 * @package tinymcerte
 * @subpackage classfile
 */

namespace TinyMCERTE;

use modX;

/**
 * Class TinyMCERTE
 */
class TinyMCERTE
{
    /**
     * A reference to the modX instance
     * @var modX $modx
     */
    public $modx;

    /**
     * The namespace
     * @var string $namespace
     */
    public $namespace = 'tinymcerte';

    /**
     * The package name
     * @var string $packageName
     */
    public $packageName = 'TinyMCE Rich Text Editor';

    /**
     * The version
     * @var string $version
     */
    public $version = '0';

    /**
     * The class options
     * @var array $options
     */
    public $options = [];

    /**
     * TinyMCERTE constructor
     *
     * @param modX $modx A reference to the modX instance.
     * @param array $options An array of options. Optional.
     */
    public function __construct(modX &$modx, $options = [])
    {
        $this->modx =& $modx;
        $this->namespace = $this->getOption('namespace', $options, $this->namespace);

        $corePath = $this->getOption('core_path', $options, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/' . $this->namespace . '/');
        $assetsPath = $this->getOption('assets_path', $options, $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/' . $this->namespace . '/');
        $assetsUrl = $this->getOption('assets_url', $options, $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/' . $this->namespace . '/');
        $modxversion = $this->modx->getVersionData();
        $this->version = $this->getVersionData();

        // Load some default paths for easier management
        $this->options = array_merge([
            'namespace' => $this->namespace,
            'version' => $this->version,
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'vendorPath' => $corePath . 'vendor/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'pluginsPath' => $corePath . 'elements/plugins/',
            'controllersPath' => $corePath . 'controllers/',
            'processorsPath' => $corePath . 'processors/',
            'templatesPath' => $corePath . 'templates/',
            'assetsPath' => $assetsPath,
            'assetsUrl' => $assetsUrl,
            'jsUrl' => $assetsUrl . 'js/',
            'cssUrl' => $assetsUrl . 'css/',
            'connectorUrl' => $assetsUrl . 'connector.php'
        ], $options);

        $lexicon = $this->modx->getService('lexicon', 'modLexicon');
        $lexicon->load($this->namespace . ':default');

        $this->packageName = $this->modx->lexicon('tinymcerte');

        // Add default options
        $this->options = array_merge($this->options, [
            'debug' => (bool)$this->getOption('debug', $options, false),
            'modxversion' => $modxversion['version'],
        ]);
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
    public function getOption($key, $options = [], $default = null)
    {
        $option = $default;
        if (!empty($key) && is_string($key)) {
            if ($options != null && array_key_exists($key, $options)) {
                $option = $options[$key];
            } elseif (array_key_exists($key, $this->options)) {
                $option = $this->options[$key];
            } elseif (array_key_exists("$this->namespace.$key", $this->modx->config)) {
                $option = $this->modx->getOption("$this->namespace.$key");
            }
        }
        return $option;
    }

    /**
     * Get the TinyMCE language code by the manager language
     *
     * @param $language
     * @return string
     */
    public function getLanguageCode($language)
    {
        $codes = [
            'bg' => 'bg_BG',
            'fr' => 'fr_FR',
            'he' => 'he_IL',
            'pt-br' => 'pt_BR',
            'sv' => 'sv_SE',
            'th' => 'th_TH',
            'zh' => 'zh_CN',
        ];

        if (isset($codes[$language])) {
            $language = $codes[$language];
        }

        $langFile = $this->getOption('assetsPath') . 'mgr/tinymce/langs/' . $language . '.js';
        if (!file_exists(($langFile))) {
            return 'en';
        }

        return $language;
    }

    /**
     * Explode and clean comma separaded setting values
     *
     * @param string $string
     * @param string $delimiter
     * @return array
     */
    public function explodeAndClean($string, $delimiter = ',')
    {
        $array = explode($delimiter, $string); // Explode fields to array
        $array = array_map('trim', $array);  // Trim array's values
        $array = array_keys(array_flip($array)); // Remove duplicate fields
        return array_filter($array); // Remove empty values from array
    }

    private function getVersionData() {
        $version = $this->getOption('lit');
        if (empty($version)) {
            $version = $this->modx->version['full_version'];
        }
        return $version;
    }
}
