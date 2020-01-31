import Plugin from '@ckeditor/ckeditor5-core/src/plugin';
import { downcastElementToElement } from "@ckeditor/ckeditor5-engine/src/conversion/downcast-converters";
import ModelRange from "@ckeditor/ckeditor5-engine/src/model/range";

const label = {
    en: 'Non-breaking space',
    cs: 'Nedělitelná mezera',
};

export default class NonBreakingSpacePlugin extends Plugin {
    init() {
        const editor = this.editor;
        this.title = label[editor.locale.language] || label.en;

        editor.editing.view.document.on('keydown', (event, data) => {
            if (data.shiftKey === true && data.keyCode === 32) {
                data.preventDefault();
                event.stop();


                editor.model.change(writer => {
                    const imageElement = writer.createElement('nbspStrict');
                    console.log(imageElement);

                    editor.model.insertContent(imageElement, editor.model.document.selection);
                });
            }
        });

        editor.model.schema.register('nbspStrict', {
            isBlock: true,
            isLimit: false,
            allowIn: '$block',
        });

        editor.conversion.for('editingDowncast').add(downcastElementToElement({
            model: 'nbspStrict', view: this.createNbspElement.bind(this)
        }));

        editor.conversion.for('dataDowncast').add(
            downcastElementToElement({model: 'nbspStrict', view: this.createSpanElement})
        );
        editor.conversion.for('upcast').add(this.upcast());


    }

    createSpanElement(href, writer) {

        return writer.createUIElement('span', null, function (domDocument) {
            const domElement = this.toDomElement(domDocument);
            domElement.innerHTML = '&nbsp;';

            return domElement;
        });
    }

    createNbspElement(href, writer) {
        const title = this.title;
        return writer.createEmptyElement('span', {
            class: 'ck-non-breaking-space',
            title: title,
            contentEditable: false
        }, function (domDocument) {
            const domElement = this.toDomElement(domDocument);
            domElement.innerHTML = ' ';

            return domElement;
        });
    }

    upcast() {
        return dispatcher => {
            dispatcher.on('element:span', (evt, data, conversionApi) => {
                const viewItem = data.viewItem;

                if (!viewItem || !conversionApi.consumable.consume(viewItem, {name: true})) {
                    return;
                }

                if (viewItem.isEmpty && viewItem._attrs.entries.length === 0) {
                    const space = conversionApi.writer.createElement('nbspStrict');
                    // const conversionResult = conversionApi.convertItem( space );
                    conversionApi.writer.insert(space, data.modelCursor);
                    data.modelRange = ModelRange._createFromPositionAndShift(data.modelCursor, space.offsetSize);
                    data.modelCursor = data.modelRange.end;
                }
            }, {priority: 'high'});
        };
    }
}
