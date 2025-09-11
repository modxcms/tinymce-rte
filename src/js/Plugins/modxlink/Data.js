import {collectNodesInRange, isImageFigure} from "./Functions";
import Optional from "./Optional";

export default class Data {
    constructor(editor) {
        this.editor = editor;
        window.editor = editor;
        this.element = this.getAnchorElement(editor, this.editor.selection.getNode())?.value ?? null;
        if (this.element === null) {
            let testElement = document.createElement('div');
            testElement.innerHTML = this.editor.selection.getContent();
            for (let i = 0; i < testElement.children.length; i++) {
                if (testElement.children[i].tagName === 'A') {
                    this.element = testElement.children[i];
                    this.editor.selection.select(this.element);
                    break;
                }
            }
        }

        this.initialData = {
            link_text: this.editor.selection.getContent(),
            link_title: '',
            aria_label: '',
            aria_labelledby: '',
            aria_describedby: '',
            aria_hidden: false,
            id: '',
            rel: '',
            classes: '',
            new_window: false,
            'page_page': '',
            'page_url': '',
            'page_anchor': '',
            'page_parameters': '',
            'url_url': '',
            'email_to': '',
            'email_subject': '',
            'email_body': '',
            'phone_phone': '',
            'file_file': { value: '', metadata: undefined }
        };
        
        this.activeTab = 'url';
        this.data = this.parseData();
    }

    getLinksInSelection = rng => collectNodesInRange(rng, isLink);
    getAnchorElement = (editor, selectedElm) => {
        selectedElm = selectedElm || this.getLinksInSelection(editor.selection.getRng())[0] || editor.selection.getNode();
        if (isImageFigure(selectedElm)) {
            return Optional.from(editor.dom.select('a[href]', selectedElm)[0]);
        } else {
            return Optional.from(editor.dom.getParent(selectedElm, 'a[href]'));
        }
    };
    
    getData() {
        return this.data;
    }
    
    getActiveTab() {
        return this.activeTab;
    }

    parseData() {
        const elementData = this.getElementData();
        this.activeTab = elementData.tab;
        
        return {
            ...(this.initialData),
            ...(elementData.data)
        };
    }
    
    getElementData() {
        if (!this.element) return {};
        console.info(this.element);

        
        this.editor.selection.select(this.element);
        
        const data = {
            link_text: this.editor.selection.getContent(),
            link_title: '',
            classes: '',
            new_window: false,
            ...(this.initialData || {})

        };

        data.link_title = this.element.getAttribute('title') ?? '';
        data.classes = this.element.getAttribute('class') ?? '';
        data.id = this.element.getAttribute('id') ?? '';
        data.rel = this.element.getAttribute('rel') ?? '';
        data.aria_label = this.element.getAttribute('aria-label') ?? '';
        data.aria_labelledby = this.element.getAttribute('aria-labelledby') ?? '';
        data.aria_describedby = this.element.getAttribute('aria-describedby') ?? '';
        data.aria_hidden = this.element.getAttribute('aria-hidden') ?? false;
        data.new_window = (this.element.getAttribute('target') === '_blank');
        data.link_text = this.element.innerHTML;

        const linkType = this.element.dataset.linkType;
        let url = this.element.getAttribute('href')  ?? '';

        if (linkType === 'page') {
            data.page_page = this.element.getAttribute('data-link-page') ?? '';
            data.page_anchor = this.element.getAttribute('data-link-anchor') ?? '';
            data.page_parameters = this.element.getAttribute('data-link-parameters') ?? '';

            if (data.page_page || data.page_anchor || data.page_parameters) {
                data.page_url = url.replace(('#' + data.page_anchor), '');
                data.page_url = data.page_url.replace(('?' + data.page_parameters), '');
    
                return {
                    tab: 'page',
                    data,
                };
            }
        }

        if (linkType === 'email') {
            if (url.slice(0, 7) === 'mailto:') {
                url = url.slice(7);
                url = url.split('?');

                data.email_to = url[0];
                if (url[1]) {
                    const components = url[1].split('&');
                    components.forEach(component => {
                        component = component.split('=');
                        if (component[0] === 'subject') {
                            data.email_subject = decodeURI(component[1]);
                        }

                        if (component[0] === 'body') {
                            data.email_body = decodeURI(component[1]);
                        }
                    });
                }

                return {
                    tab: 'email',
                    data,
                }
            }
        }

        if (linkType === 'phone') {
            if (url.slice(0, 4) === 'tel:') {
                data.phone_phone = url.slice(4);

                return {
                    tab: 'phone',
                    data,
                }
            }
        }

        if (linkType === 'file') {
            data.file_file = { value: url, metadata: undefined };
            
            return {
                tab: 'file',
                data,
            }
        }

        data.url_url = url;

        return {
            tab: 'url',
            data,
        };
    }
}
