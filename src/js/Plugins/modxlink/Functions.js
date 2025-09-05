export const collectNodesInRange = (rng, predicate) => {
    if (rng.collapsed) {
        return [];
    } else {
        const contents = rng.cloneContents();
        const firstChild = contents.firstChild;
        const walker = new tinymce.util.Tools.resolve('tinymce.dom.TreeWalker')(firstChild, contents);
        const elements = [];
        let current = firstChild;
        do {
            if (predicate(current)) {
                elements.push(current);
            }
        } while (current = walker.next());
        return elements;
    }
};
export const isNullable = a => a === null || a === undefined;
export const isNonNullable = a => !isNullable(a);
export const isImageFigure = elm => isNonNullable(elm) && elm.nodeName === 'FIGURE' && /\bimage\b/i.test(elm.className);