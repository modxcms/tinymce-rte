Ext.ns('TinyMCERTE');
TinyMCERTE.browserCallback = function(data) {
    top.tinymce.activeEditor.windowManager.getParams().oninsert(data.fullRelativeUrl);
    top.tinymce.activeEditor.windowManager.close()
};