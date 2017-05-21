<?php

class TinyMCERTEOnRichTextEditorInit extends TinyMCERTEPlugin {

    public function init(){
        $useEditor = $this->modx->getOption('use_editor', false);
        $whichEditor = $this->modx->getOption('which_editor', '');

        if ($useEditor && $whichEditor == 'TinyMCE RTE') return true;

        return false;
    }

    public function process() {
        $html = $this->initTinyMCE();

        $this->modx->event->output($html);
    }

    private function initTinyMCE() {
        $this->modx->controller->addJavascript($this->tinymcerte->getOption('jsUrl') . 'vendor/tinymce/tinymce.min.js');
        $this->modx->controller->addJavascript($this->tinymcerte->getOption('jsUrl') . 'vendor/autocomplete.js');
        $this->modx->controller->addJavascript($this->tinymcerte->getOption('jsUrl') . 'mgr/tinymcerte.js');

        return '<script type="text/javascript">
            Ext.ns("TinyMCERTE");
            TinyMCERTE.editorConfig = ' . $this->modx->toJSON($this->getTinyConfig()) . ';

            Ext.onReady(function(){
                TinyMCERTE.loadForTVs();
            });
        </script>';
    }

    private function getTinyConfig() {
        $language = $this->modx->getOption('manager_language');
        $language = $this->tinymcerte->getLanguageCode($language);

        $objectResizing = $this->tinymcerte->getOption('object_resizing', array(), '1');

        if ($objectResizing === '1' || $objectResizing === 'true') {
            $objectResizing = true;
        }

        if ($objectResizing === '0' || $objectResizing === 'false') {
            $objectResizing = false;
        }

        $config = array_merge(array(
            'plugins' => $this->tinymcerte->getOption('plugins', array(), 'advlist autolink lists link modximage charmap print preview anchor visualblocks searchreplace code fullscreen insertdatetime media table contextmenu paste modxlink'),
            'toolbar1' => $this->tinymcerte->getOption('toolbar1', array(), 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image'),
            'toolbar2' => $this->tinymcerte->getOption('toolbar2', array(), ''),
            'toolbar3' => $this->tinymcerte->getOption('toolbar3', array(), ''),
            'modxlinkSearch' => $this->tinymcerte->getOption('jsUrl').'vendor/tinymce/plugins/modxlink/search.php',
            'language' => $language,
            'directionality' => $this->modx->getOption('manager_direction', array(), 'ltr'),
            'menubar' => $this->tinymcerte->getOption('menubar', array(), 'file edit insert view format table tools'),
            'statusbar' => $this->tinymcerte->getOption('statusbar', array(), 1) == 1,
            'image_advtab' => $this->tinymcerte->getOption('image_advtab', array(), true) == 1,
            'paste_as_text' => $this->tinymcerte->getOption('paste_as_text', array(), false) == 1,
            'style_formats_merge' => $this->tinymcerte->getOption('style_formats_merge', array(), false) == 1,
            'object_resizing' => $objectResizing,
            'link_class_list' => $this->modx->fromJSON($this->tinymcerte->getOption('link_class_list', array(), '[]')),
            'browser_spellcheck' => $this->tinymcerte->getOption('browser_spellcheck', array(), false) == 1,
            'content_css' => $this->tinymcerte->explodeAndClean($this->tinymcerte->getOption('content_css', array(), '')),
            'image_class_list' => $this->modx->fromJSON($this->tinymcerte->getOption('image_class_list', array(), '[]')),
            'skin' => $this->tinymcerte->getOption('skin', array(), 'modx'),
            'relative_urls' => $this->tinymcerte->getOption('relative_urls', array(), true) == 1,
            'remove_script_host'=> $this->tinymcerte->getOption('remove_script_host', array(), true) == 1,
        ), $this->getProperties());

        $styleFormats = $this->tinymcerte->getOption('style_formats', array(), '[]');
        $styleFormats = $this->modx->fromJSON($styleFormats);

        $finalFormats = array();

        foreach ($styleFormats as $format) {
            if (!isset($format['items'])) continue;

            $items = $this->tinymcerte->getOption($format['items'], array(), '[]');
            $items = $this->modx->fromJSON($items);

            if (empty($items)) continue;

            $format['items'] = $items;

            $finalFormats[] = $format;
        }

        if (!empty($finalFormats)) {
            $config['style_formats'] = $finalFormats;
        }

        $validElements = $this->tinymcerte->getOption('valid_elements');
        if(!empty($validElements)){
            $config['valid_elements'] = $validElements;
        }

        $externalConfig = $this->tinymcerte->getOption('external_config');
        if (!empty($externalConfig)) {
            $externalConfig = str_replace('{base_path}', $this->modx->getOption('base_path'), $externalConfig);
            $externalConfig = str_replace('{core_path}', $this->modx->getOption('core_path'), $externalConfig);
            $externalConfig = str_replace('{assets_path}', $this->modx->getOption('assets_path'), $externalConfig);
            
            if (file_exists($externalConfig) && is_readable($externalConfig)) {
                $externalConfig = file_get_contents($externalConfig);
                $externalConfig = $this->modx->fromJSON($externalConfig);
                if (is_array($externalConfig)) {
                    $config = array_merge($config, $externalConfig);
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
    private function getProperties() {
        $props = $this->scriptProperties;
        // unset the regular properties sent by resource controllers
        unset($props['editor'], $props['elements'], $props['id'], $props['resource'], $props['mode']);

        return $props;
    }
}
