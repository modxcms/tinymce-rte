Ext.ns('TinyMCERTE');

TinyMCERTE.Tiny = function(config) {
    Ext.apply(this.cfg, config, {});

    TinyMCERTE.Tiny.superclass.constructor.call(this,{});
};

Ext.extend(TinyMCERTE.Tiny,Ext.Component,{
    cfg: {
        selector: '#ta'
        ,document_base_url: MODx.config.base_url
        ,file_browser_callback_types: 'file image media'
        ,plugins: [
            "advlist autolink lists link image charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime media table contextmenu paste"
        ]
        ,toolbar1: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image'
    }

    ,editorConfig: {}

    ,initComponent: function() {
        TinyMCERTE.Tiny.superclass.initComponent.call(this);

        Ext.onReady(this.render, this);
    }

    ,render: function() {
        Ext.apply(this.cfg, this.editorConfig, {});
        this.cfg.file_browser_callback = this.loadBrowser;

        tinymce.init(this.cfg);
    }
    
    ,loadBrowser: function(field_name, url, type, win) {
        tinyMCE.activeEditor.windowManager.open({
            title: "MODX Resource Browser",
            url: MODx.config['manager_url'] + 'index.php?a=' + MODx.action['browser'] + '&source=' + MODx.config['default_media_source'],
            width: 1000,
            height: 500
        }, {
            oninsert: function(url) {
                win.document.getElementById(field_name).value = url;
            }
        });

        return false;
    }
});

MODx.loadRTE = function(id) {
    new TinyMCERTE.Tiny({
        selector: '#' + id
    });
};