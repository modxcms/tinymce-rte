<?php
/**
 * @package tinymcerte
 * @subpackage plugin
 */

namespace TinyMCERTE\Plugins\Events;

use TinyMCERTE\Plugins\Plugin;
use modResource;
use xPDO;

/**
 * Class OnRichTextEditorInit
 */
class OnRichTextEditorInit extends Plugin
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
            // Load the tinymcerte scripts, when the editor is TinyMCE RTE
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
        $html = $this->initTinyMCE();

        $this->modx->event->output($html);
    }

    /**
     * Register TinyMCE scripts and return the configuration
     *
     * @return string
     */
    private function initTinyMCE()
    {
        $tinyURL = $this->getTinyMCEURL();
        $this->modx->controller->addJavascript($tinyURL . '?v=' . $this->tinymcerte->version);
        $this->modx->controller->addJavascript($this->tinymcerte->getOption('assetsUrl') . 'mgr/tinymcerte.min.js?v=' . $this->tinymcerte->version);
        $this->modx->controller->addCss($this->tinymcerte->getOption('assetsUrl') . 'mgr/tinymcerte.css?v=' . $this->tinymcerte->version);

        $configstring = json_encode($this->getTinyConfig(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if (json_last_error()) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'TinyMCE RTE configuration can\'t be encoded as JSON: ', json_last_error_msg());
        }
        // unescape escaped regular expressions, that can't be contained directly in the external config
        $configstring = preg_replace_callback('/"##(.*?)##"/', function ($matches) {
            // replace double backslashes with single ones that have to be set for a valid json in the external config
            return str_replace(['\\\\'], ['\\'], $matches[1]);
        }, $configstring);

        $this->modx->controller->addHtml('<script type="text/javascript">
            Ext.ns("TinyMCERTE");
            TinyMCERTE.editorConfig = ' . $configstring . ';
        </script>');

        return '<script type="text/javascript">
            Ext.onReady(function(){
                TinyMCERTE.loadForTVs();
            });
        </script>';
    }

    /**
     * Get the TinyMCE configuration
     *
     * @return array
     */
    private function getTinyConfig()
    {
        switch ($this->tinymcerte->getOption('modxversion')) {
            case 3:
                $managerlanguage = $this->modx->getOption('cultureKey');
                break;
            default:
                $managerlanguage = $this->modx->getOption('manager_language');
                break;
        }
        $language = $this->tinymcerte->getLanguageCode($managerlanguage);
        $objectResizing = $this->tinymcerte->getOption('object_resizing', [], '1');
        if ($objectResizing === '1' || $objectResizing === 'true') {
            $objectResizing = true;
        } elseif ($objectResizing === '0' || $objectResizing === 'false') {
            $objectResizing = false;
        }

        /** @var modResource $resource */
        $resource = $this->modx->getOption('resource', $this->scriptProperties);
        if ($resource && $resource->get('context_key')) {
            $context = $this->modx->getContext($resource->get('context_key'));
            $documentBaseUrl = $context->getOption('site_url');
        } else {
            $documentBaseUrl = $this->modx->getOption('site_url');
        }

        $config = array_merge([
            'plugins' => $this->tinymcerte->getOption('plugins', [], 'advlist autoresize autolink lists charmap preview anchor visualblocks searchreplace code fullscreen insertdatetime media table image quickbars modxlink modai'),
            'quickbars_insert_toolbar' => $this->tinymcerte->getOption('insert_toolbar', [], 'image media quicktable modxlink modai_generate'),
            'quickbars_selection_toolbar' => $this->tinymcerte->getOption('selection_toolbar', [], 'bold italic underline | modxlink | modai_enhance'),
            'toolbar1' => $this->tinymcerte->getOption('toolbar1', [], 'undo redo | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | modxlink | image'),
            'toolbar2' => $this->tinymcerte->getOption('toolbar2', [], ''),
            'toolbar3' => $this->tinymcerte->getOption('toolbar3', [], ''),
            'connector_url' => $this->tinymcerte->getOption('connectorUrl'),
            'language' => $language,
            'directionality' => $this->modx->getOption('manager_direction', [], 'ltr'),
            'menubar' => $this->tinymcerte->getOption('menubar', [], 'file edit insert view format table tools'),
            'statusbar' => $this->tinymcerte->getOption('statusbar', [], true) == 1,
            'image_advtab' => $this->tinymcerte->getOption('image_advtab', [], true) == 1,
            'paste_as_text' => $this->tinymcerte->getOption('paste_as_text', [], false) == 1,
            'style_formats_merge' => $this->tinymcerte->getOption('style_formats_merge', [], false) == 1,
            'object_resizing' => $objectResizing,
            'link_class_list' => json_decode($this->tinymcerte->getOption('link_class_list', [], '[]'), true),
            'browser_spellcheck' => $this->tinymcerte->getOption('browser_spellcheck', [], false) == 1,
            'content_css' => $this->tinymcerte->explodeAndClean($this->tinymcerte->getOption('content_css', [], '')),
            'image_class_list' => json_decode($this->tinymcerte->getOption('image_class_list', [], '[]'), true),
            'skin' => $this->tinymcerte->getOption('skin', [], 'oxide'),
            'relative_urls' => $this->tinymcerte->getOption('relative_urls', [], true) == 1,
            'document_base_url' => $documentBaseUrl,
            'remove_script_host' => $this->tinymcerte->getOption('remove_script_host', [], true) == 1,
            'entity_encoding' => $this->tinymcerte->getOption('entity_encoding', [], 'named'),
            'enable_link_list' => $this->tinymcerte->getOption('enable_link_list', [], true) == 1,
            'enable_link_aria' => $this->tinymcerte->getOption('enable_link_aria', [], false) == 1,
            'max_height' => (int)$this->tinymcerte->getOption('max_height', [], 500),
            'min_height' => (int)$this->tinymcerte->getOption('min_height', [], 100),
            'branding' => $this->tinymcerte->getOption('branding', [], false) == 1,
            'cache_suffix' => '?v=' . $this->tinymcerte->version,
            'promotion' => false
        ], $this->getSettings(), $this->getProperties());

        $styleFormats = $this->tinymcerte->getOption('style_formats', [], '[]');
        $styleFormats = json_decode($styleFormats, true) ?? [];
        $finalFormats = [];
        foreach ($styleFormats as $format) {
            if (!isset($format['items'])) {
                continue;
            } elseif (is_array($format['items'])) {
                $items = $format['items'];
            } else {
                $items = $this->tinymcerte->getOption($format['items'], [], '[]');
                $items = json_decode($items, true);
            }
            if (empty($items)) {
                continue;
            }
            $format['items'] = $items;
            $finalFormats[] = $format;
        }
        if (!empty($finalFormats)) {
            $config['style_formats'] = $finalFormats;
        }

        $validElements = $this->tinymcerte->getOption('valid_elements');
        if (!empty($validElements)) {
            $config['valid_elements'] = $validElements;
        }

        $externalConfig = $this->tinymcerte->getOption('external_config');
        if (!empty($externalConfig)) {
            $externalConfig = str_replace([
                '{base_path}',
                '{core_path}',
                '{assets_path}',
            ], [
                $this->modx->getOption('base_path'),
                $this->modx->getOption('core_path'),
                $this->modx->getOption('assets_path'),
            ], $externalConfig);
            if (file_exists($externalConfig) && is_readable($externalConfig)) {
                $externalConfig = file_get_contents($externalConfig);
                $externalConfig = json_decode($externalConfig, true);
                if (is_array($externalConfig)) {
                    $config = array_replace_recursive($config, $externalConfig);
                }
            }
        }
        return $config;
    }

    /**
     * Get properties passed to OnRichTextEditorInit event, minus the ones set by the resource controllers
     *
     * @return array
     */
    private function getProperties()
    {
        $props = $this->scriptProperties;
        // unset the regular properties sent by resource controllers
        unset($props['editor'], $props['elements'], $props['id'], $props['resource'], $props['mode']);
        foreach ($props as $key => $prop) {
            if (is_object($prop)) {
                unset($props[$key]);
            }
        }

        return $props;
    }

    /**
     * Get settings from a JSON encoded array in tinymcerte.settings system setting
     *
     * @return array
     */
    private function getSettings()
    {
        $settings = json_decode($this->tinymcerte->getOption('settings'), true);
        return ($settings) ?: [];
    }

    private function getTinyMCEURL()
    {
        $tinyURL = $this->tinymcerte->getOption('tiny_url');
        if (empty($tinyURL)) {
            $tinyURL = $this->modx->getOption('tinymcerte.tiny_url', null, $this->tinymcerte->getOption('assetsUrl') . 'mgr/tinymce/tinymce.min.js');
        }
        if (strpos($tinyURL, '{') !== false) {
            $tinyURL = str_replace([
                '{base_url}',
                '{assets_url}',
                '{tinymcerte.assets_url}',
            ], [
                $this->modx->getOption('base_url'),
                $this->modx->getOption('assets_url'),
                $this->tinymcerte->getOption('assetsUrl'),
            ], $tinyURL);
        }
        return $tinyURL;
    }
}
