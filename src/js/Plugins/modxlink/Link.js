export default class Link {
    constructor(editor) {
        this.editor = editor;
        this.element = editor.dom.getParent(editor.selection.getStart(), 'a[href]')
    }
    
    insertLink(linkText, attributes) {
        this.editor.insertContent(this.editor.dom.createHTML('a', attributes, linkText));
    }
    
    editLink(linkText, attributes) {
        if (!this.element) return this.insertLink(linkText, attributes);

        this.editor.focus();
        this.editor.dom.removeAllAttribs(this.element);

        for (let attribute in attributes) {
            if (attributes.hasOwnProperty(attribute)) {
                this.editor.dom.setAttrib(this.element, attribute, attributes[attribute]);
            }
        }

        this.element.innerHTML = linkText;
        this.editor.selection.select(this.element);
    }
    
    handleLink(linkText, attributes) {
        if (!this.element) return this.insertLink(linkText, attributes);

        return this.editLink(linkText, attributes);
    }
    
    static getGeneralAttributes(data, type) {
        const attributes = {
            'data-fred-link-type': type
        };
        
        if (data.link_title) {
            attributes.title = data.link_title;
        }

        if (data.new_window) {
            attributes.target = '_blank';
        }

        if (data.classes) {
            attributes.class = data.classes;
        }
        
        return attributes;
    }
    
    savePage(data) {
        if (!data.page_page && !data.page_anchor && !data.page_parameters) return;

        const attributes = {
            ...(Link.getGeneralAttributes(data, 'page')),
            'data-fred-link-page': data.page_page
        };
        attributes.href = data.page_url;

        if (data.page_anchor) {
            attributes['data-fred-link-anchor'] = data.page_anchor;
            attributes.href = `${data.page_url}#${data.page_anchor}`;
        }
        if (data.page_parameters) {
            attributes['data-fred-link-parameters'] = data.page_parameters;
            attributes.href = `${attributes.href}?${data.page_parameters}`;
        }
        
        return this.handleLink(data.link_text, attributes);
    }
    
    saveUrl(data) {
        if (!data.url_url) return;

        return this.handleLink(data.link_text, {
            ...(Link.getGeneralAttributes(data, 'url')),
            href: data.url_url
        });
    }
    
    saveEmail(data) {
        if (!data.email_to) return;

        let href = `mailto:${data.email_to}`;
        const mailAttrs = [];

        if (data.email_subject) {
            mailAttrs.push('subject=' + encodeURI(data.email_subject));
        }

        if (data.email_body) {
            mailAttrs.push('body=' + encodeURI(data.email_body));
        }

        if (mailAttrs.length > 0) {
            href += '?' + mailAttrs.join('&');
        }
        
        return this.handleLink(data.link_text, {
            ...(Link.getGeneralAttributes(data, 'email')),
            href
        });
    }
    
    savePhone(data) {
        if (!data.phone_phone) return;
        
        return this.handleLink(data.link_text, {
            ...(Link.getGeneralAttributes(data, 'phone')),
            href: `tel:${data.phone_phone}`
        });
    }
    
    saveFile(data) {
        if (!data.file_file) return;
        
        return this.handleLink(data.link_text, {
            ...(Link.getGeneralAttributes(data, 'file')),
            href: data.file_file.value
        });
    }
    
    save(type, data) {
        switch (type) {
            case 'page':
                this.savePage(data);
                break;
            case 'url':
                this.saveUrl(data);
                break;
            case 'email':
                this.saveEmail(data);
                break;
            case 'phone':
                this.savePhone(data);
                break;
            case 'file':
                this.saveFile(data);
                break;
        }

        this.editor.selection.collapse(false);
    }
}
