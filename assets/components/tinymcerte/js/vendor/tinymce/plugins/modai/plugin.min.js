tinymce.PluginManager.add('modai', function(editor, url) {
    if (typeof modAI === 'undefined') {
        return;
    }

    const defaultParams = {
        namespace: 'tinymcerte.modai',
        field: editor.id,
    };

    const getEnhancePrompts = () => {
        try {
            return JSON.parse(MODx.config[`tinymcerte.modai.${editor.id}.text.modify_prompts`] || MODx.config[`tinymcerte.modai.global.text.modify_prompts`] || '{}');
        } catch {
            return [];
        }
    }

    const formatEnhancePrompts = (item, selection, disabled) => {
        if (item.prompts) {
            if (!item.label) return null;

            return {
                type: 'nestedmenuitem',
                text: item.label,
                getSubmenuItems: () => {
                    return item.prompts.map((n) => {
                        return formatEnhancePrompts(n, selection, disabled);
                    }).filter(Boolean);
                }
            }
        }

        if (!item.label || !item.prompt) return null;

        return {
            type: 'menuitem',
            text: item.label,
            disabled: disabled,
            onAction: async function () {
                await generate(item.prompt, selection);

            }
        };
    }

    const freeTextPrompt = async (api, cache, prompt, selectedText) => {
        try {
            const result = await modAI.executor.mgr.prompt.freeText({
                ...defaultParams,
                prompt,
                context: selectedText,
            }, (data) => {
                cache.insert({prompt, content: data.content}, true);
            });
            cache.insert({prompt, content: result.content});
            api.unblock();
        } catch (err) {
            api.unblock();
            tinymce.activeEditor.windowManager.alert(`Failed: ${err.message}`);
        }
    }

    const openTextPromptWindow = (selectedText) => {
        const windowAPI = editor.windowManager.open({
            title: 'Generate Content',
            size: 'medium',
            body: {
                type: 'panel',
                items: [
                    {
                        type: 'textarea',
                        name: 'prompt',
                        label: 'Prompt',
                        multiline: true,
                    },
                    {
                        type: 'button',
                        name: 'generate',
                        text: 'Generate',
                    },
                    {
                        type: 'htmlpanel',
                        html: `<iframe style="width:100%" id="tinymcerte-${editor.id}-modai-iframe">` +
                            '<!DOCTYPE html>' +
                            '<html>' +
                            '<head>' +
                            editor.contentCSS.map((css) => {
                                return `<link rel="stylesheet" type="text/css" href="${css}" />`;
                            }).join('') +
                            '</head>' +
                            `<body></body>` +
                            '</html>' +
                            '</iframe>'
                    }
                ]
            },
            buttons: [
                {
                    text: '<<',
                    type: 'custom',
                    align: 'start',
                    name: 'prev',
                    disabled: true
                },
                {
                    text: '>>',
                    type: 'custom',
                    align: 'start',
                    name: 'next',
                    disabled: true
                },
                {
                    text: 'Insert',
                    type: 'submit',
                    name: 'submit',
                    primary: true,
                    disabled: true
                },
                {
                    text: 'Cancel',
                    type: 'cancel'
                }
            ],
            onAction: async function (api, details) {
                if (details.name === 'prev') {
                    cache.prev();
                    return;
                }

                if (details.name === 'next') {
                    cache.next();
                    return;
                }

                if (details.name === 'generate') {
                    const data = api.getData();
                    const prompt = data.prompt;

                    api.block("Generating");

                    await freeTextPrompt(api, cache, prompt, selectedText);
                }
            },
            onSubmit: function (api) {
                const data = cache.getData();
                if (data.value) {
                    editor.insertContent(data.value);
                }

                api.close();
            }
        });

        const cache = modAI.history.init(`tinymce.${editor.id}_text`, (data, noStore) => {
            if (!data.value) return;

            windowAPI.enable('submit')

            windowAPI.setData({
                prompt: data.value.prompt,
            });

            const iframeDocument = document.getElementById(`tinymcerte-${editor.id}-modai-iframe`).contentDocument;
            iframeDocument.body.innerHTML = data.value.content;

            if (noStore) {
                iframeDocument.body.scrollTop = iframeDocument.body.scrollHeight;
            }

            if (data.prevStatus) {
                windowAPI.enable('prev');
            } else {
                windowAPI.disable('prev');
            }

            if (data.nextStatus) {
                windowAPI.enable('next');
            } else {
                windowAPI.disable('next');
            }
        });
        cache.syncUI();
        windowAPI.setData({
            prompt: "",
        });

        return {windowAPI, cache};
    }

    const openImagePromptWindow = () => {
        const windowAPI = editor.windowManager.open({
            title: 'Generate Image',
            size: 'medium',
            body: {
                type: 'panel',
                items: [
                    {
                        type: 'textarea',
                        name: 'prompt',
                        label: 'Prompt',
                        multiline: true,
                    },
                    {
                        type: 'button',
                        name: 'generate',
                        text: 'Generate',
                    },
                    {
                        type: 'htmlpanel',
                        name: 'iframe',
                        html: `<div id="modai_${editor.id}_iframe" />`
                    }
                ]
            },
            buttons: [
                {
                    text: '<<',
                    type: 'custom',
                    align: 'start',
                    name: 'prev',
                    disabled: true
                },
                {
                    text: '>>',
                    type: 'custom',
                    align: 'start',
                    name: 'next',
                    disabled: true
                },
                {
                    text: 'Insert',
                    type: 'submit',
                    name: 'submit',
                    primary: true,
                    disabled: true
                },
                {
                    text: 'Cancel',
                    type: 'cancel'
                }
            ],
            onAction: async function (api, details) {
                if (details.name === 'prev') {
                    cache.prev();
                    return;
                }

                if (details.name === 'next') {
                    cache.next();
                    return;
                }

                if (details.name === 'generate') {
                    const data = api.getData();
                    const prompt = data.prompt;

                    api.block("Generating");

                    try {
                        const result = await modAI.executor.mgr.prompt.image({
                            ...defaultParams,
                            prompt,
                        });
                        cache.insert({ prompt, ...result });
                        api.unblock();
                    } catch (err) {
                        api.unblock();
                        tinymce.activeEditor.windowManager.alert(`Failed: ${err.message}`);
                    }
                }
            },
            onSubmit: async function (api) {
                api.block("Downloading");

                const params = {
                    ...defaultParams,
                };

                const cacheData = cache.getData().value;
                if (cacheData.url) {
                    params.url = cacheData.url;
                }

                if (cacheData.base64) {
                    params.image = cacheData.base64;
                }

                try {
                    const res = await modAI.executor.mgr.download.image(params)
                    editor.insertContent(`<img src="${res.fullUrl}" />`);
                    api.close();
                } catch (err) {
                    api.unblock();
                    tinymce.activeEditor.windowManager.alert(`Failed: ${err.message}`);
                }
            }
        });

        const cache = modAI.history.init(`tinymce.${editor.id}_image`, (data) => {
            if (!data.value) return;

            windowAPI.enable('submit')
            const preview = document.getElementById(`modai_${editor.id}_iframe`);
            preview.innerHTML =`<img style="max-height:450px;max-width: 100%" src="${data.value?.url || data.value.base64}" />`;

            windowAPI.setData({
                prompt: data.value.prompt,
            });

            if (data.prevStatus) {
                windowAPI.enable('prev');
            } else {
                windowAPI.disable('prev');
            }

            if (data.nextStatus) {
                windowAPI.enable('next');
            } else {
                windowAPI.disable('next');
            }
        });
        cache.syncUI();
        windowAPI.setData({
            prompt: "",
        });
    }

    const generate = async (prompt, selection) => {
        const {windowAPI, cache} = openTextPromptWindow(selection);

        windowAPI.block("Generating");
        await freeTextPrompt(windowAPI, cache, prompt, selection);
    }

    editor.ui.registry.addButton('modai_generate', {
        text: '✦ Prompt',
        onAction: function () {
            const selectedText = editor.selection.getContent({format: 'text'});
            openTextPromptWindow(selectedText);
        }
    });

    editor.ui.registry.addButton('modai_generate_image', {
        text: '✦ Image',
        onAction: function () {
            openImagePromptWindow();
        }
    });

    editor.ui.registry.addMenuItem('modai_generate', {
        text: '✦ Prompt',
        onAction: function () {
            openTextPromptWindow(selectedText);
        }
    });

    editor.ui.registry.addMenuItem('modai_generate_image', {
        text: '✦ Image',
        onAction: function () {
            openImagePromptWindow();
        }
    });

    editor.ui.registry.addContextMenu('modai_generate', {
        update: function (el) {
            return 'modai_generate';
        }
    });

    editor.ui.registry.addContextMenu('modai_generate_image', {
        update: function (el) {
            const selectedText = editor.selection.getContent({ format: 'text' });
            if (selectedText) {
                return '';
            }

            return 'modai_generate_image';
        }
    });

    const prompts = getEnhancePrompts();
    if (prompts.length > 0) {
        editor.ui.registry.addMenuButton('modai_enhance', {
            text: '✦ Modify',
            fetch: function (cb) {
                const selection = editor.selection.getContent({format: 'text'});

                cb(prompts.map((prompt) => {
                    return formatEnhancePrompts(prompt, selection, !selection);
                }).filter(Boolean));
            }
        });

        editor.ui.registry.addNestedMenuItem('modai_enhance', {
            text: '✦ Modify',
            getSubmenuItems: function() {
                const selection = editor.selection.getContent({format: 'text'});
                return prompts.map((prompt) => {
                    return formatEnhancePrompts(prompt, selection, !selection);
                }).filter(Boolean);
            }
        });
    }
    return {
        getMetadata: function () {
            return {
                name: "modAI Plugin",
                url: url
            };
        }
    };
});


