import Plugin from '@ckeditor/ckeditor5-core/src/plugin';
import Range from '@ckeditor/ckeditor5-engine/src/view/range';
import Position from '@ckeditor/ckeditor5-engine/src/view/position';

export default class ImageLink extends Plugin {
    /**
     * @inheritDoc
     */
    static get requires() {
        return [ /*ImageLinkUI, ImageLinkEditing*/ ];
    }

    /**
     * @inheritDoc
     */
    static get pluginName() {
        return 'ImageLink';
    }

    init() {
        const editor = this.editor;
        editor.model.schema.extend('image', {allowAttributes: ['linkHref']});
        editor.conversion.for('upcast').add(upcastLink());
        editor.conversion.for('upcast').add(upcastImageLink('img'));
        // // editor.conversion.for('upcast').add(upcastImageLink('figure'));
        editor.conversion.for('dataDowncast').add(downcastDataImageLink());
        editor.conversion.for('editingDowncast').add(downcastEditorImageLink());
        //
        this.listenTo(  this.editor.editing.view.document, 'click', (event, data) => {
            // prevent redirecting when clicking on image
            if (data.domTarget.nodeName === 'IMG' && data.domTarget.parentElement.nodeName === 'A') {
                data.domEvent.preventDefault();
            }
        } );
    }
}

/**
 * Returns converter for links that wraps <img> or <figure> elements.
 *
 * @returns {Function}
 */
function upcastLink() {
    return dispatcher => {
        dispatcher.on( 'element:a', ( evt, data, conversionApi ) => {
            const viewLink = data.viewItem;
            const imageInLink = Array.from( viewLink.getChildren() ).find( child => child.name === 'img' || child.name === 'figure' );

            if ( imageInLink ) {
                // There's an image (or figure) inside an <a> element - we consume it so it won't be picked up by Link plugin.
                const consumableAttributes = { attributes: [ 'href' ] };

                // Consume the link so the default one will not convert it to $text attribute.
                if ( !conversionApi.consumable.test( viewLink, consumableAttributes ) ) {
                    // Might be consumed by something else - ie other converter with priority=highest - a standard check.
                    return;
                }

                // Consume 'href' attribute from link element.
                conversionApi.consumable.consume( viewLink, consumableAttributes );
            }
        }, { priority: 'high' } );
    };
}

function upcastImageLink( elementName ) {
    return dispatcher => {
        dispatcher.on( `element:${ elementName }`, ( evt, data, conversionApi ) => {
            const viewImage = data.viewItem;
            const parent = viewImage.parent;

            // Check only <img>/<figure> that are direct children of a link.
            if ( parent.name === 'a' ) {
                const modelImage = Array.from( data.modelRange.getItems() ).find( item => item.is( 'image' ) );
                const linkHref = parent.getAttribute( 'href' );

                if ( modelImage && linkHref ) {
                    // Set the href attribute from link element on model image element.
                    conversionApi.writer.setAttribute( 'linkHref', linkHref, modelImage );
                    console.log('Settings linkHref to ', linkHref);
                }
            }
        }, { priority: 'normal' } );
    };
}


function downcastEditorImageLink() {
    return dispatcher => {
        dispatcher.on( 'attribute:linkHref:image', ( evt, data, conversionApi ) => {
            const href = data.attributeNewValue;
            let viewImage = conversionApi.mapper.toViewElement( data.item );
            conversionApi.writer.setAttribute('linkHref', href, viewImage);
        // .setCustomProperty( imageSymbol, true, viewElement );

        }, { priority: 'normal' } );
    };
}

function downcastDataImageLink() {
    return dispatcher => {
        dispatcher.on( 'attribute:linkHref:image', ( evt, data, conversionApi ) => {
            const href = data.attributeNewValue;
            // The image will be already converted - so it will be present in the view.
            let viewImage = conversionApi.mapper.toViewElement( data.item );

            // Below will wrap already converted image by newly created link element.

            // 1. Create empty link element.
            const linkElement = conversionApi.writer.createContainerElement( 'a', { href } );

            // 2. Insert link before associated image.
            if (viewImage.name === 'figure') {
                viewImage = viewImage._children[0];
            }
            conversionApi.writer.insert( Position._createBefore( viewImage ), linkElement );

            // 3. Move whole converted image to a link.
            conversionApi.writer.move( Range._createOn( viewImage ), new Position( linkElement, 0 ) );
        }, { priority: 'normal' } );
    };
}
