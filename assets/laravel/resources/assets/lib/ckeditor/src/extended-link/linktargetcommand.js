/**
 * @module link/linktargetcommand
 */

import Command from '@ckeditor/ckeditor5-core/src/command';
import findLinkRange from "@ckeditor/ckeditor5-link/src/findlinkrange";

/**
 * The link command. It is used by the {@link module:link/link~Link link feature}.
 *
 * @extends module:core/command~Command
 */
export default class LinkTargetCommand extends Command {
    /**
     * The value of the `'linkTarget'` attribute if the start of the selection is located in a node with this attribute.
     *
     * @observable
     * @readonly
     * @member {Object|undefined} #value
     */

    /**
     * @inheritDoc
     */
    refresh () {
        const model = this.editor.model;
        const doc = model.document;

        this.value = doc.selection.getAttribute('linkTarget');
        this.isEnabled = model.schema.checkAttributeInSelection(doc.selection, 'linkHref');
    }

    /**
     * Executes the command.
     *
     * @fires execute
     * @param {String} target Link target.
     */
    execute (target) {
        const model = this.editor.model;
        const selection = model.document.selection;

        model.change(writer => {
            // If selection is collapsed then update selected link or insert new one at the place of caret.
            if (selection.isCollapsed) {
                // When selection is inside text with `linkHref` attribute.
                if (selection.hasAttribute('linkHref')) {
                    // Then update `linkHref` value.
                    const linkRange = findLinkRange(selection.getFirstPosition(), selection.getAttribute('linkHref'), model);

                    writer.setAttribute('linkTarget', target, linkRange);
                    if (target === '_blank') {
                        writer.setAttribute('linkRel', 'noopener noreferrer', linkRange);
                    } else {
                        writer.setAttribute('linkRel', null, linkRange);
                    }

                    // Create new range wrapping changed link.
                    writer.setSelection(linkRange);
                }
            } else {
                // If selection has non-collapsed ranges, we change attribute on nodes inside those ranges
                // omitting nodes where `linkHref` attribute is disallowed.
                const ranges = model.schema.getValidRanges(selection.getRanges(), 'linkHref');

                for (const range of ranges) {
                    writer.setAttribute('linkTarget', target, range);
                    if (target === '_blank') {
                        writer.setAttribute('linkRel', 'noopener noreferrer', range);
                    } else {
                        writer.setAttribute('linkRel', null, range);
                    }
                }
            }
        });
    }
}
