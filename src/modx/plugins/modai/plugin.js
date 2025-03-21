tinymce.PluginManager.add('modai', function(editor, url) {
    if (typeof modAI === 'undefined') {
        return;
    }

    const modAIPromptHandler = () => {
        const selectedText = editor.selection.getContent({format: 'text'});

        return modAI.ui.localChat({
            namespace: 'tinymcerte.modai',
            field: editor.id,
            key: editor.id,
            resource: (MODx.request.a.toLowerCase() === 'resource/update') ? MODx.request.id : undefined,
            availableTypes: ['text', 'image'],
            context: selectedText,
            customCSS: editor.contentCSS,
            textActions: {
                insert: (msg, modal) => {
                    editor.insertContent(msg.content);
                    modal.api.closeModal();
                }
            },
            imageActions: {
                copy: false,
                insert: (msg, modal) => {
                    editor.insertContent(`<img src="${msg.ctx.fullUrl}" />`);
                    modal.api.closeModal();
                }
            }
        });
    }

    const getEnhancePrompts = () => {
        try {
            return JSON.parse(MODx.config[`tinymcerte.modai.${editor.id}.text.modify_prompts`] || MODx.config[`tinymcerte.modai.global.text.modify_prompts`] || '{}');
        } catch {
            return [];
        }
    }

    const formatEnhancePrompts = (item, disabled) => {
        if (item.prompts) {
            if (!item.label) return null;

            return {
                type: 'nestedmenuitem',
                text: item.label,
                getSubmenuItems: () => {
                    return item.prompts.map((n) => {
                        return formatEnhancePrompts(n, disabled);
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
                await generate(item.prompt);

            }
        };
    }

    const generate = async (prompt) => {
        const modal = modAIPromptHandler();

        modal.api.sendMessage(prompt, true);
    }

    editor.ui.registry.addButton('modai_generate', {
        text: '✦ Prompt',
        onAction: () => {
            modAIPromptHandler();
        }
    });

    editor.ui.registry.addMenuItem('modai_generate', {
        text: '✦ Prompt',
        onAction: () => {
            modAIPromptHandler();
        }
    });

    editor.ui.registry.addContextMenu('modai_generate', {
        update: function (el) {
            return 'modai_generate';
        }
    });

    const prompts = getEnhancePrompts();
    if (prompts.length > 0) {
        editor.ui.registry.addMenuButton('modai_enhance', {
            text: '✦ Modify',
            fetch: function (cb) {
                const selection = editor.selection.getContent({format: 'text'});

                cb(prompts.map((prompt) => {
                    return formatEnhancePrompts(prompt, !selection);
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


