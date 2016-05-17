Ext.ns('TinyMCERTE');

TinyMCERTE.Tiny = function(config, editorConfig) {
    Ext.apply(this.cfg, editorConfig, {});

    TinyMCERTE.Tiny.superclass.constructor.call(this, config);
};

Ext.extend(TinyMCERTE.Tiny,Ext.Component,{
    cfg: {
        selector: '#ta'
        ,document_base_url: MODx.config.base_url
        ,file_browser_callback_types: 'file image media'
    }

    ,allowDrop: false

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
            editor.on('change', function () {   // Keep synced textarea and iframe on each change
                tinymce.triggerSave();
            });
            var saveKey = MODx.config.keymap_save || 's';
            editor.addShortcut('ctrl+' + saveKey, '', function () {
                var btn = Ext.getCmp('modx-abtn-save');
                if (!btn) return;
                if (btn.disabled) return;
                if(!btn.keys) return;

                var found = false;
                Ext.each(btn.keys, function(key) {
                    if (key.ctrl == true && key.key == saveKey) {
                        found = true;
                        return false;
                    }
                });

                if (!found) return;

                btn.el.dom.click();
            });

            if (that.allowDrop) {
                that.registerDrop();
            }
        };

        tinymce.init(this.cfg);
    }
    
    ,loadBrowser: function(field_name, url, type, win) {
        tinyMCE.activeEditor.windowManager.open({
            title: "MODX Resource Browser",
            url: MODx.config['manager_url'] + 'index.php?a=' + MODx.action['browser'] + '&source=' + MODx.config['default_media_source'] + (MODx.ctx ? ('&ctx=' + MODx.ctx) : ''),
            width: 1000,
            height: 500
        }, {
            oninsert: function(url) {
                win.document.getElementById(field_name).value = url;
            }
        });

        return false;
    }

    ,registerDrop: function() {
        var editor = this.editor;
        var fakeDiv = null;
        var ddTarget = new Ext.Element(this.editor.getContainer());
        var ddTargetEl = ddTarget.dom;

        var insert = {
            text: function(text) {
                editor.insertContent(text);
                editor.focus();
            }
            ,link: function(id, text) {
                editor.insertContent('<a href="[[~' + id + ']]" title="' + text + '">' + text + '</a>');
                editor.focus();
            }
            ,image: function(path) {
                editor.insertContent('<img src="' + path + '">');
                editor.focus();
            }
        };

        var dropTarget = new Ext.dd.DropTarget(ddTargetEl, {
            ddGroup: 'modx-treedrop-dd'

            ,_notifyEnter: function(ddSource, e, data) {
                fakeDiv = Ext.DomHelper.insertAfter(ddTarget, {tag: 'div', style: 'position: absolute;top: 0;left: 0;right: 0;bottom: 0;'});
                ddTarget.frame();
                editor.focus();
            }
            ,notifyOut: function(ddSource, e, data) {
                fakeDiv && fakeDiv.remove();
                ddTarget.on('mouseover', onMouseOver);
            }
            ,notifyDrop: function(ddSource, e, data) {
                console.log(data);
                fakeDiv && fakeDiv.remove();
                var v = '';
                var win = false;
                switch (data.node.attributes.type) {
                    case 'modResource':
                        insert.link(data.node.attributes.pk, data.node.text.replace(/\s*<.*>.*<.*>/, ''));
                        break;
                    case 'snippet':
                        win = true;
                        break;
                    case 'chunk':
                        win = true;
                        break;
                    case 'tv':
                        win = true;
                        break;
                    case 'file':
                        var imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'svg'];

                        var ext = data.node.attributes.text.substring(data.node.attributes.text.lastIndexOf('.')+1);
                        if (imageTypes.indexOf(ext) != -1) {
                            insert.image(data.node.attributes.url);
                        } else {
                            insert.text(data.node.attributes.url);
                        }

                        break;
                    default:
                        var dh = Ext.getCmp(data.node.attributes.type+'-drop-handler');
                        if (dh) {
                            return dh.handle(data,{
                                ddTargetEl: ddTargetEl
                                ,cfg: cfg
                                ,iframe: true
                                ,iframeEl: ddTargetEl
                                ,onInsert: insert.text
                            });
                        }
                        return false;
                        break;
                }

                if (win) {
                    var r = {
                        pk: data.node.attributes.pk
                        ,classKey: data.node.attributes.classKey
                        ,name: data.node.attributes.name
                        ,output: v
                        ,ddTargetEl: ddTargetEl
                        ,cfg: {onInsert: insert.text}
                        ,iframe: true
                        ,onInsert: insert.text
                    };

                    if (TinyMCERTE.insertWindow) {
                        TinyMCERTE.insertWindow.destroy();
                        TinyMCERTE.insertWindow.close();
                    }
                    TinyMCERTE.insertWindow = MODx.load({
                        xtype: 'modx-window-insert-element'
                        ,record: r
                        ,closeAction: 'close'
                        ,listeners: {
                            'success':{fn: function() {},scope:this}
                            ,'close': {fn:function() { this.destroy(); this.close(); }}
                        }
                    });
                    TinyMCERTE.insertWindow.setValues(r);
                    TinyMCERTE.insertWindow.show();
                }

                return true;
            }
        });

        dropTarget.addToGroup('modx-treedrop-elements-dd');
        dropTarget.addToGroup('modx-treedrop-sources-dd');

        var onMouseOver = function(e){
            if (Ext.dd.DragDropMgr.dragCurrent) {
                dropTarget._notifyEnter();
                ddTarget.un('mouseover', onMouseOver);
            }
        };
        ddTarget.on('mouseover', onMouseOver);

        this.on('destroy', function() {
            dropTarget.destroy();
        });
    }
});

TinyMCERTE.loadForTVs = function() {
    new TinyMCERTE.Tiny({
        allowDrop: false
    },{
        selector: '.modx-richtext'
    });
};

MODx.loadRTE = function(id) {
    new TinyMCERTE.Tiny({
        allowDrop: true
    },{
        selector: '#' + id
    });
};
