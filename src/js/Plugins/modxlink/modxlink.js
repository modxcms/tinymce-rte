import Choices from 'choices.js';

import Data from './Data';
import Link from './Link';
import { unlinkSelection } from './Unlink';

export default (editor, url) => {

    editor.options.register('link_class_list', {
        processor: 'object[]',
        default: []
    });
    const handleClick = () => {
        const dataHelper = new Data(editor);
        let currentTab = dataHelper.getActiveTab() ?? 'page';

        const data = dataHelper.getData();
        const lookupCache = {};
        let lookupTimeout = null;
        let initData = [];
        const formsize = 40;
        const choicesData = {
            'page_page': data.page_page,
            'page_url': data.page_url,
        };

        const buildListItems = (inputList, itemCallback, startItems) => {
            function appendItems(values, output)
            {
                output = output || [];

                tinymce.each(values, function (item) {
                    var menuItem = {text: item.text || item.title};

                    if (item.items) {
                        menuItem.items = appendItems(item.items);
                    } else if (item.menu) {
                        menuItem.items = appendItems(item.menu);
                    } else {
                        menuItem.value = item.value;

                        if (itemCallback) {
                            itemCallback(menuItem);
                        }
                    }

                    output.push(menuItem);
                });

                return output;
            }

            return appendItems(inputList, startItems || []);
        };

        const linkOptions = [{
            type: 'input',
            label: 'Link Text',
            name: 'link_text',
            size: formsize,
        }];
        linkOptions.push({
            type: 'input',
            name: 'link_title',
            label: 'Link Title',
            size: formsize,

        });
        if (editor.options.get('link_class_list').length) {
            linkOptions.push({
                type: 'listbox',
                name: 'classes',
                label: 'Classes',
                size: formsize,
                items: buildListItems(
                    editor.options.get('link_class_list'),
                    function (item) {

                        if (item.value) {
                            item.textStyle = function () {
                                return editor.formatter.getCssText({inline: 'a', classes: [item.value]});
                            };
                        }
                    }
                ),
            });
        }else{
            linkOptions.push({
                type: 'input',
                name: 'classes',
                size: formsize,
                label: 'Classes',
            });
        }

        linkOptions.push({
            type: 'checkbox',
            name: 'new_window',
            size: formsize,
            label: 'New Window',
        });

        const linkOptionsPanel = {
            type: 'panel',
            items: linkOptions
        };

        const tabPanel = {
            type: 'tabpanel',
            tabs: [
                {
                    title: 'Page',
                    name: 'page',
                    items: [
                        linkOptionsPanel,
                        {
                            id: 'pagecontainer',
                            type: 'htmlpanel',
                            html: '<input type="hidden" name="page_page" /><label for="page_url" class="tox-label">Page Title</label><select id="page_url"></select>'
                        },
                        {
                            type: 'input',
                            label: 'Anchor Tag',
                            id: 'page_anchor',
                            name: 'page_anchor',
                            size: formsize,
                        },
                        {
                            type: 'input',
                            label: 'Extra Params',
                            id: 'page_parameters',
                            name: 'page_parameters',
                            size: formsize,
                        },
                    ]
                },
                {
                    title: 'URL',
                    name: 'url',
                    items: [
                        linkOptionsPanel,
                        {
                            type: 'input',
                            label: 'URL',
                            name: 'url_url',
                            size: formsize,
                        }
                    ]
                },
                {
                    title: 'Email',
                    name: 'email',
                    items: [
                        linkOptionsPanel,
                        {
                            type: 'input',
                            label: 'To',
                            name: 'email_to',
                            size: formsize,
                        },
                        {
                            type: 'input',
                            label: 'Subject',
                            size: formsize,
                            name: 'email_subject',
                        },
                        {
                            type: 'input',
                            multiline: true,
                            label: 'Body',
                            size: formsize,
                            name: 'email_body',
                        }
                    ]
                },
                {
                    title: 'Phone',
                    name: 'phone',
                    items: [
                        linkOptionsPanel,
                        {
                            type: 'input',
                            label: 'Phone',
                            name: 'phone_phone',
                            size: formsize,
                        }
                    ]
                },
                {
                    title: 'File',
                    name: 'file',
                    items: [
                        linkOptionsPanel,
                        {
                            type: 'urlinput',
                            label: 'File',
                            name: 'file_file',
                            size: formsize,
                        }
                    ]
                }
            ]
        };

        let node = editor.selection.getNode()
        const linkState = (node.nodeName == "A");

        let templateInputChoices;
        const initChoices = () => {
            const input = document.querySelector('#page_url');
            if (input) {
                templateInputChoices = new Choices(input, {
                    removeItemButton: true
                });

                input.addEventListener('search', event => {
                    clearTimeout(lookupTimeout);
                    lookupTimeout = setTimeout(serverLookup, 200);
                });

                input.addEventListener('choice', event => {
                    templateInputChoices.setChoices(initData, 'value', 'pagetitle', true);

                    choicesData.page_page = event.detail.choice.value;
                    choicesData.page_url = event.detail.choice.customProperties.url;

                    if (!data.link_text) {
                        data.link_text = event.detail.choice.label;
                        linkText.value(event.detail.choice.label);
                    }

                    const pageAnchorEl = document.getElementById('page_anchor-l');
                    if (pageAnchorEl) {
                        pageAnchorEl.innerText =  'Block on' + event.detail.choice.label;
                    }
                });

                input.addEventListener('removeItem', event => {
                    if (templateInputChoices.getValue()) return;

                    choicesData.page_page = '';
                    choicesData.page_url = '';
                    const pageAnchorEl = document.getElementById('page_anchor-l');
                    if (pageAnchorEl) {
                        pageAnchorEl.innerText = 'Block on' + ( MODx?.activePage?.record?.pagetitle || 'Page');
                    }
                });
            }
        }

        // Open window
        const win = editor.windowManager.open({
            title: 'Link to',
            initialData: data,
            buttons: [
                {
                    text: 'Ok',
                    type: 'submit',
                },{
                    text: linkState ? 'Remove Link' : 'Cancel',
                    name: 'remove',
                    type: 'custom',
                }
            ],
            onTabChange: (dialogApi, details) => {
                currentTab = details.newTabName;

                initChoices();
            },
            onSubmit: (api) => {
                const link = new Link(editor);
                link.save(currentTab, {...api.getData(), ...choicesData});
                api.close();
            },
            onAction: (api, details) => {
                if (details.name !== 'remove') return;

                if (linkState) {
                    const el = editor.dom.getParent(editor.selection.getStart(), 'a[href]');
                    editor.selection.select(el);
                    unlinkSelection(editor);
                }

                api.close();
            },
            body: tabPanel,
        });
        win.showTab(currentTab);

        document.querySelectorAll('.tox-dialog').forEach((item) => {
            item.classList.add('mce-fred--modxlink');
        });


        const populateOptions = options => {
            const toRemove = [];

            /* templateInputChoices.currentState.items.forEach(item => {
                if (item.active) {
                    toRemove.push(item.value);
                }
            });*/

            const toKeep = [];
            options.forEach(option => {
                if (toRemove.indexOf(option.id) === -1) {
                    toKeep.push(option);
                }
            });

            templateInputChoices.setChoices(toKeep, 'value', 'pagetitle', true);
        };
        const serverLookup = () => {
            const query = templateInputChoices.input.value;
            if (query in lookupCache) {
                populateOptions(lookupCache[query]);
            } else {
                // @todo implement server lookup
                console.info('TinyMCE RTE Choices server lookup');
                const resourceSearchUrl = TinyMCERTE.editorConfig.connector_url + '?action=mgr/resource/search' + (MODx.ctx ? ('&wctx=' + MODx.ctx) : '') + '&HTTP_MODAUTH=' + MODx.siteId + '&query=' + query + '&limit=10&sort=pagetitle&dir=ASC';
                fetch(resourceSearchUrl).then(function (response) {
                    return response.json();
                }).then(function (data) {
                    populateOptions(data.results);
                    lookupCache[query] = data.results;
                })
            }
        };

        initChoices();
    }

    editor.ui.registry.addButton('modxlink', {
        icon: 'link',
        tooltip: 'Insert/edit link',
        onAction: handleClick,
        stateSelector: 'a[href]'
    });

    editor.ui.registry.addMenuItem('modxlink', {
        icon: 'link',
        tooltip: 'Insert/edit link',
        onAction: handleClick,
        stateSelector: 'a[href]'
    });

    return {
        getMetadata: function () {
            return {
                name: "MODX Link",
                url: "https://modx.com"
            };
        }
    };
}
