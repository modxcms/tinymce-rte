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
    }

    ,initComponent: function() {
        TinyMCERTE.Tiny.superclass.initComponent.call(this);

        Ext.onReady(this.render, this);
    }

    ,editor: null

    ,render: function() {
        var that = this;
        Ext.apply(this.cfg, TinyMCERTE.editorConfig, {});
        this.cfg.file_browser_callback = this.loadBrowser;
        this.cfg.init_instance_callback = function(editor) {
            that.editor = editor;
        };

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

TinyMCERTE.loadForTVs = function() {
    new TinyMCERTE.Tiny({
        selector: '.modx-richtext'
    });
};

MODx.loadRTE = function(id) {
    new TinyMCERTE.Tiny({
        selector: '#' + id
    });
};