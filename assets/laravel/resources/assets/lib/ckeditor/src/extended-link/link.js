import LinkPlugin from '@ckeditor/ckeditor5-link/src/link';
import LinkEditing from "@ckeditor/ckeditor5-link/src/linkediting";
import ExtendedLinkUI from "./linkui";
import { downcastAttributeToElement } from '@ckeditor/ckeditor5-engine/src/conversion/downcast-converters';
import { upcastAttributeToAttribute } from '@ckeditor/ckeditor5-engine/src/conversion/upcast-converters';
import LinkTargetCommand from "./linktargetcommand";
import UnlinkCommand from "./unlinkcommand";

export default class ExtendedLinkPlugin extends LinkPlugin {
    init () {
        const editor = this.editor;

        editor.commands.add('linktarget', new LinkTargetCommand(editor));
        editor.commands.add('unlink', new UnlinkCommand(editor));

        this.initLinkRel();
        this.initLinkTarget();
    }

    initLinkTarget () {
        const editor = this.editor;

        editor.model.schema.extend('$text', { allowAttributes: 'linkTarget' });

        editor.conversion.for('downcast').add(downcastAttributeToElement({
            model: 'linkTarget',
            view: (attributeValue, writer) => {
                return writer.createAttributeElement('a', { target: attributeValue }, { priority: 5 });
            },
            converterPriority: 'low'
        }));

        editor.conversion.for('upcast').add(upcastAttributeToAttribute({
            view: {
                name: 'a',
                key: 'target'
            },
            model: 'linkTarget',
            converterPriority: 'low'
        }));
    }

    initLinkRel () {
        const editor = this.editor;

        editor.model.schema.extend('$text', { allowAttributes: 'linkRel' });

        editor.conversion.for('downcast').add(downcastAttributeToElement({
            model: 'linkRel',
            view: (attributeValue, writer) => {
                return writer.createAttributeElement('a', { rel: attributeValue }, { priority: 5 });
            },
            converterPriority: 'low'
        }));

        editor.conversion.for('upcast').add(upcastAttributeToAttribute({
            view: {
                name: 'a',
                key: 'rel'
            },
            model: 'linkRel',
            converterPriority: 'low'
        }));
    }

    /**
     * @inheritDoc
     */
    static get requires () {
        return [LinkEditing, ExtendedLinkUI];
    }
}
