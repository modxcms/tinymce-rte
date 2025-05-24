export default class Data {
    constructor(editor) {
        this.editor = editor;
        window.editor = editor;
        this.element = editor.dom.getParent(editor.selection.getStart(), 'a[href]');
        const textarea = document.createElement('textarea');
        textarea.innerHTML = this.editor.selection.getContent();
        
        this.initialData = {
            link_text: this.editor.selection.getContent(),
            link_title: '',
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
        data.new_window = (this.element.getAttribute('target') === '_blank');
        data.link_text = this.element.innerHTML;

        const linkType = this.element.dataset.fredLinkType;
        let url = this.element.getAttribute('href')  ?? '';

        if (linkType === 'page') {
            data.page_page = this.element.getAttribute('data-fred-link-page') ?? '';
            data.page_anchor = this.element.getAttribute('data-fred-link-anchor') ?? '';
            data.page_parameters = this.element.getAttribute('data-fred-link-parameters') ?? '';

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
