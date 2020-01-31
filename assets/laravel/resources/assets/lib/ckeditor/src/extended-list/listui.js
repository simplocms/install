/**
 * @license Copyright (c) 2003-2018, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md.
 */

/**
 * @module list/listui
 */

import numberedListIcon from '@ckeditor/ckeditor5-list/theme/icons/numberedlist.svg';
import bulletedListIcon from '@ckeditor/ckeditor5-list/theme/icons/bulletedlist.svg';

import ListUIPlugin from '@ckeditor/ckeditor5-list/src/listui';
import ButtonView from '@ckeditor/ckeditor5-ui/src/button/buttonview';
import { createDropdown, addToolbarToDropdown } from '@ckeditor/ckeditor5-ui/src/dropdown/utils';

/**
 * The list UI feature. It introduces the `'numberedList'` and `'bulletedList'` buttons that
 * allow to convert paragraphs to and from list items and indent or outdent them.
 */
export default class ListUI extends ListUIPlugin {
    /**
     * @inheritDoc
     */
    init() {
        // Create two buttons and link them with numberedList and bulletedList commands.
        const t = this.editor.t;

        const types = this.editor.config.get('list.types');

        if (types && Array.isArray(types) && types.length) {
            const componentFactory = this.editor.ui.componentFactory;

            this._addButton('bulletedList:default', t('Bulleted List'));

            types.forEach(option =>
                this._addButton(`bulletedList:${ option.name }`, option.title)
            );

            componentFactory.add('bulletedList', locale => {
                const dropdownView = createDropdown(locale);

                // Add existing alignment buttons to dropdown's toolbar.
                const normalListButton = componentFactory.create(`bulletedList:default`);
                const buttons = types.map(option => componentFactory.create(`bulletedList:${ option.name }`));
                addToolbarToDropdown(dropdownView, [normalListButton, ...buttons]);

                // Configure dropdown properties an behavior.
                dropdownView.buttonView.set({
                    label: t('Bulleted List'),
                    tooltip: true
                });

                dropdownView.toolbarView.isVertical = true;

                // The default icon is align left as we do not support RTL yet (see #3).
                const defaultIcon = bulletedListIcon;

                // Change icon to reflect current selection's alignment.
                dropdownView.buttonView.bind('icon').toMany(buttons, 'isOn', (...areActive) => {
                    // Get the index of an active button.
                    const index = areActive.findIndex(value => value);

                    // If none of the commands is active, display either defaultIcon or the first button's icon.
                    if (index < 0) {
                        return defaultIcon;
                    }

                    // Return active button's icon.
                    return buttons[index].icon;
                });

                // Enable button if any of the buttons is enabled.
                dropdownView.bind('isEnabled').toMany(buttons, 'isEnabled', (...areEnabled) => areEnabled.some(isEnabled => isEnabled));

                return dropdownView;
            });

            this._initStyles(types);
        } else {
            this._addButton('bulletedList', t('Bulleted List'), bulletedListIcon);
        }

        this._addButton('numberedList', t('Numbered List'), numberedListIcon);
    }

    _initStyles(types) {
        const id = 'ck-editor-list-styles';

        if (document.getElementById(id)) {
            return;
        }

        let styleContent = '.ck-content ul{list-style: initial;}';
        for (const ti in types) {
            const type = types[ti];
            let typeStyle = ``;

            if (type.image) {
                typeStyle += `list-style-image: url('${ type.image }');`;
            }

            if (type.type) {
                typeStyle += `list-style-type: ${ type.type };`;
            }

            if (type.position) {
                typeStyle += `list-style-position: ${ type.position };`;
            }

            if (typeStyle) {
                styleContent += `.ck-content ul.${ type.name } {${ typeStyle }}`;
            }
        }

        if (!styleContent) {
            return;
        }

        const style = document.createElement('style');
        style.id = id;
        style.type = 'text/css';
        style.innerHTML = styleContent;
        document.getElementsByTagName('head')[0].appendChild(style);
    }

    /**
     * Helper method for initializing a button and linking it with an appropriate command.
     *
     * @private
     * @param {String} commandName The name of the command.
     * @param {Object} label The button label.
     * @param {String=} icon The source of the icon.
     */
    _addButton(commandName, label, icon) {
        const editor = this.editor;

        editor.ui.componentFactory.add(commandName, locale => {
            const command = editor.commands.get(commandName);

            const buttonView = new ButtonView(locale);

            buttonView.set({
                label,
                icon,
                tooltip: true,
                withText: !icon
            });

            // Bind button model to command.
            buttonView.bind('isOn', 'isEnabled').to(command, 'value', 'isEnabled');

            // Execute command.
            this.listenTo(buttonView, 'execute', () => editor.execute(commandName));

            return buttonView;
        });
    }
}
