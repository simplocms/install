/**
 * @module image-link/image-link-command
 */

import Command from '@ckeditor/ckeditor5-core/src/command';
import { isImage } from '@ckeditor/ckeditor5-image/src/image/utils';

/**
 * The image text alternative command. It is used to change the `alt` attribute of `<image>` elements.
 *
 * @extends module:core/command~Command
 */
export default class ImageLinkCommand extends Command {
	/**
	 * The command value: `false` if there is no `alt` attribute, otherwise the value of the `alt` attribute.
	 *
	 * @readonly
	 * @observable
	 * @member {String|Boolean} #value
	 */

	/**
	 * @inheritDoc
	 */
	refresh() {
		const element = this.editor.model.document.selection.getSelectedElement();

		this.isEnabled = isImage( element );

		if ( isImage( element ) && element.hasAttribute( 'alt' ) ) {
			this.value = element.getAttribute( 'alt' );
		} else {
			this.value = false;
		}
	}

	/**
	 * Executes the command.
	 *
	 * @fires execute
	 * @param {Object} options
	 * @param {String} options.newValue The new value of the `alt` attribute to set.
	 */
	execute( options ) {
		const model = this.editor.model;
		const imageElement = model.document.selection.getSelectedElement();

		model.change( writer => {
			writer.setAttribute( 'alt', options.newValue, imageElement );
		} );
	}
}
