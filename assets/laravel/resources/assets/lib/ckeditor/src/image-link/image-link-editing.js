/**
 * @module image-link/image-link-editing
 */

import Plugin from '@ckeditor/ckeditor5-core/src/plugin';
import ImageLinkCommand from "./image-link-command";

/**
 * The image text alternative editing plugin.
 *
 * Registers the `'imageLink'` command.
 *
 * @extends module:core/plugin~Plugin
 */
export default class ImageLinkEditing extends Plugin {
	/**
	 * @inheritDoc
	 */
	init() {
		this.editor.commands.add( 'imageLink', new ImageLinkCommand( this.editor ) );
	}
}
