<?php
/**
 * @package tinymcerte
 * @subpackage plugin
 */

namespace TinyMCERTE\Plugins\Events;

use TinyMCERTE\Plugins\Plugin;

class OnRichTextEditorInit extends Plugin
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
        $html = $this->initTinyMCE();

        $this->modx->event->output($html);
    }

    private function initTinyMCE(): string
    {
        $this->modx->controller->addJavascript($this->tinymcerte->getOption('jsUrl') . 'vendor/tinymce/tinymce.min.js?v=' . $this->tinymcerte->version);
        $this->modx->controller->addJavascript($this->tinymcerte->getOption('jsUrl') . 'mgr/tinymcerte.min.js?v=' . $this->tinymcerte->version);
        $this->modx->controller->addCss($this->tinymcerte->getOption('cssUrl') . 'mgr/tinymcerte.css?v=' . $this->tinymcerte->version);

        return '<script type="text/javascript">
            Ext.ns("TinyMCERTE");
            TinyMCERTE.editorConfig = ' . json_encode($this->getTinyConfig(), JSON_PRETTY_PRINT) . ';

            Ext.onReady(function(){
                TinyMCERTE.loadForTVs();
            });
        </script>';
    }

    private function getTinyConfig(): array
    {
        $language = $this->tinymcerte->getLanguageCode($this->modx->getOption('manager_language'));
        $objectResizing = $this->tinymcerte->getOption('object_resizing', [], '1');
        if ($objectResizing === '1' || $objectResizing === 'true') {
            $objectResizing = true;
        } elseif ($objectResizing === '0' || $objectResizing === 'false') {
            $objectResizing = false;
        }

        $config = array_merge([
            'plugins' => $this->tinymcerte->getOption('plugins', [], 'advlist autolink lists charmap print preview anchor visualblocks searchreplace code fullscreen insertdatetime media table paste modximage modxlink'),
            'toolbar1' => $this->tinymcerte->getOption('toolbar1', [], 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image'),
            'toolbar2' => $this->tinymcerte->getOption('toolbar2', [], ''),
            'toolbar3' => $this->tinymcerte->getOption('toolbar3', [], ''),
            'connector_url' => $this->tinymcerte->getOption('connectorUrl'),
            'language' => $language,
            'directionality' => $this->modx->getOption('manager_direction', [], 'ltr'),
            'menubar' => $this->tinymcerte->getOption('menubar', [], 'file edit insert view format table tools'),
            'statusbar' => $this->tinymcerte->getOption('statusbar', [], 1) == 1,
            'image_advtab' => $this->tinymcerte->getOption('image_advtab', [], true) == 1,
            'paste_as_text' => $this->tinymcerte->getOption('paste_as_text', [], false) == 1,
            'style_formats_merge' => $this->tinymcerte->getOption('style_formats_merge', [], false) == 1,
            'object_resizing' => $objectResizing,
            'link_class_list' => json_decode($this->tinymcerte->getOption('link_class_list', [], '[]'), true),
            'browser_spellcheck' => $this->tinymcerte->getOption('browser_spellcheck', [], false) == 1,
            'content_css' => $this->tinymcerte->explodeAndClean($this->tinymcerte->getOption('content_css', [], '')),
            'image_class_list' => json_decode($this->tinymcerte->getOption('image_class_list', [], '[]'), true),
            'skin' => $this->tinymcerte->getOption('skin', [], 'modx'),
            'relative_urls' => $this->tinymcerte->getOption('relative_urls', [], true) == 1,
            'remove_script_host' => $this->tinymcerte->getOption('remove_script_host', [], true) == 1,
            'entity_encoding' => $this->tinymcerte->getOption('entity_encoding', [], 'named'),
            'enable_link_list' => $this->tinymcerte->getOption('enable_link_list', [], true) == 1,
            'branding' => $this->tinymcerte->getOption('branding', [], false) == 1,
            'cache_suffix' => '?v=' . $this->tinymcerte->version
        ], $this->getSettings(), $this->getProperties());

        $styleFormats = $this->tinymcerte->getOption('style_formats', [], '[]');
        $styleFormats = json_decode($styleFormats, true);
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
    private function getProperties(): array
    {
        $props = $this->scriptProperties;
        // unset the regular properties sent by resource controllers
        unset($props['editor'], $props['elements'], $props['id'], $props['resource'], $props['mode']);

        return $props;
    }

    /**
     * Get settings from a JSON encoded array in tinymcerte.settings system setting
     *
     * @return array
     */
    private function getSettings(): array
    {
        $settings = json_decode($this->tinymcerte->getOption('settings'), true);
        $settings = ($settings) ? $settings : [];

        return $settings;
    }
}
