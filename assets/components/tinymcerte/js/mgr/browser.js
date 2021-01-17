Ext.ns('TinyMCERTE');
TinyMCERTE.browserCallback = function(data) {
    if (data) {
        window.parent.postMessage({
            mceAction: 'selectFile',
            url: data.fullRelativeUrl
        }, origin);
    }
};
