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
        $this->modx->regClientStartupScript($this->tinymcerte->getOption('jsUrl') . 'vendor/tinymce/tinymce.min.js');
        $this->modx->regClientStartupScript($this->tinymcerte->getOption('jsUrl') . 'vendor/autocomplete.js');
        $this->modx->regClientStartupScript($this->tinymcerte->getOption('jsUrl') . 'mgr/tinymcerte.js');

        return '<script type="text/javascript">
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

        $config = array(
            'plugins' => $this->tinymcerte->getOption('plugins', array(), 'advlist autolink lists link image charmap print preview anchor visualblocks searchreplace code fullscreen insertdatetime media table contextmenu paste modxlink'),
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
            'link_class_list' => $this->modx->fromJSON($this->tinymcerte->getOption('link_class_list', array(), null)),
            'browser_spellcheck' => $this->tinymcerte->getOption('browser_spellcheck', array(), false) == 1,
            'content_css' => $this->tinymcerte->explodeAndClean($this->tinymcerte->getOption('content_css', array(), '')),
            'image_class_list' => $this->modx->fromJSON($this->tinymcerte->getOption('image_class_list', array(), null)),
            'convert_urls' => false,
            'link_list' => $this->getResourceList(),
        );

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

        $externalConfig = $this->tinymcerte->getOption('external_config');
        if (!empty($externalConfig)) {
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
    public function getContextList(){
        $list = array();
        $ugroups = $this->modx->user->getUserGroupNames();
        $ug = $this->modx->newQuery('modUserGroup');
        $ug->where(array('name:IN'=>$ugroups));
        $groupsin = $this->modx->getCollection('modUserGroup',$ug);
        if(!empty($groupsin)){
            foreach($groupsin as $gi){

                $webContextAccess = $this->modx->newQuery('modAccessContext');
                $webContextAccess->where(array(
                    'principal' =>$gi->get('id'),
                    'AND:target:!=' => 'mgr',
                ));
                $gi_cntx = $this->modx->getCollection('modAccessContext', $webContextAccess);

                if(!empty($gi_cntx)){
                    foreach($gi_cntx AS $acl){
                        if(!in_array($acl->get('target'), $list))
                            $list[] =$acl->get('target');
                    }
                }
            }
        }
        return $list;
    }

    public function getResourceList(){
        $contexts = $this->getContextList();
        $list = array();
        if(!empty($contexts)){
            if(count($contexts) == 1){
                $list = $this->contentItems(0,$contexts[0]);
            }else{
                foreach($contexts as $con){
                    $list[] = array("title"=>$con,"value"=>"/", "menu"=>$this->contentItems(0,$con));
                }
            }
        }
        return $list;
    }

    public function contentItems($parent = 0, $context = 'web', $level = 0){
        $c = $this->modx->newQuery('modResource');
        $c->where(array('deleted'=>0, 'context_key'=>$context, 'parent'=>$parent));
        $c->sortby('menuindex','ASC');
        $pages = $this->modx->getCollection('modResource', $c);
        $items = array();
        foreach($pages as $p){
            $id = $p->get('id');
            $title = $p->get('pagetitle');
            if($p->get('isfolder') == 0){
                $items[]= array("title"=>$title, "value"=>"[[~".$id."]]");
            }else{
                $items[]= array("title"=>$title, "value"=>"[[~".$id."]]", "menu" => $this->contentItems($id,$context, ($level + 1)));
            }

        }
        return $items;

    }

}
