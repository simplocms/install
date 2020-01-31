import LinkUI from '@ckeditor/ckeditor5-link/src/linkui';
import ExtendedLinkActionsView from "./ui/linkactionsview";
import clickOutsideHandler from '@ckeditor/ckeditor5-ui/src/bindings/clickoutsidehandler';

const linkKeystroke = 'Ctrl+K';

export default class ExtendedLinkUI extends LinkUI {
    /**
     * @inheritDoc
     */
    _createActionsView () {
        const editor = this.editor;
        const actionsView = new ExtendedLinkActionsView(editor.locale);
        const linkCommand = editor.commands.get('link');
        const unlinkCommand = editor.commands.get('unlink');

        actionsView.bind('href').to(linkCommand, 'value');
        actionsView.editButtonView.bind('isEnabled').to(linkCommand);
        actionsView.unlinkButtonView.bind('isEnabled').to(unlinkCommand);

        // Execute unlink command after clicking on the "Edit" button.
        this.listenTo(actionsView, 'edit', () => {
            this._addFormView();
        });

        // Execute unlink command after clicking on the "Unlink" button.
        this.listenTo(actionsView, 'unlink', () => {
            editor.execute('unlink');
            this._hideUI();
        });

        this.listenTo(actionsView, 'change_link_target', () => {
            const linkElement = this._getSelectedLinkElement();

            if (!linkElement) {
                return;
            }

            if (linkElement.hasAttribute('target') && linkElement.getAttribute('target') === '_blank') {
                actionsView.targetButtonView.isOn = false;
                editor.execute('linktarget', null);
            } else {
                actionsView.targetButtonView.isOn = true;
                editor.execute('linktarget', '_blank');
            }
        });

        // Close the panel on esc key press when the **actions have focus**.
        actionsView.keystrokes.set('Esc', (data, cancel) => {
            this._hideUI();
            cancel();
        });

        // Open the form view on Ctrl+K when the **actions have focus**..
        actionsView.keystrokes.set(linkKeystroke, (data, cancel) => {
            this._addFormView();
            cancel();
        });

        return actionsView;
    }

    /**
     * Attaches actions that control whether the balloon panel containing the
     * {@link #formView} is visible or not.
     *
     * @private
     */
    _enableUserBalloonInteractions() {
        const viewDocument = this.editor.editing.view.document;

        // Handle click on view document and show panel when selection is placed inside the link element.
        // Keep panel open until selection will be inside the same link element.
        this.listenTo( viewDocument, 'click', () => {
            const parentLink = this._getSelectedLinkElement();

            if ( parentLink && parentLink.name !== 'figure' ) {
                // Then show panel but keep focus inside editor editable.
                this._showUI();
            }
        } );

        // Focus the form if the balloon is visible and the Tab key has been pressed.
        this.editor.keystrokes.set( 'Tab', ( data, cancel ) => {
            if ( this._areActionsVisible && !this.actionsView.focusTracker.isFocused ) {
                this.actionsView.focus();
                cancel();
            }
        }, {
            // Use the high priority because the link UI navigation is more important
            // than other feature's actions, e.g. list indentation.
            // https://github.com/ckeditor/ckeditor5-link/issues/146
            priority: 'high'
        } );

        // Close the panel on the Esc key press when the editable has focus and the balloon is visible.
        this.editor.keystrokes.set( 'Esc', ( data, cancel ) => {
            if ( this._isUIVisible ) {
                this._hideUI();
                cancel();
            }
        } );

        // Close on click outside of balloon panel element.
        clickOutsideHandler( {
            emitter: this.formView,
            activator: () => this._isUIVisible,
            contextElements: [ this._balloon.view.element ],
            callback: () => this._hideUI()
        } );
    }

    /**
     * @inheritDoc
     */
    _getBalloonPositionData () {
        const view = this.editor.editing.view;
        const viewDocument = view.document;
        const targetLink = this._getSelectedLinkElement();

        // SET TARGET BUTTON to ACTIVE (or not)
        if (this.actionsView && targetLink) {
            this.actionsView.targetButtonView.isOn = targetLink && targetLink.getAttribute('target') === '_blank';
        }

        const target = targetLink ?
            // When selection is inside link element, then attach panel to this element.
            view.domConverter.mapViewToDom(targetLink) :
            // Otherwise attach panel to the selection.
            view.domConverter.viewRangeToDom(viewDocument.selection.getFirstRange());

        return { target };
    }

    /**
     * Returns the link {@link module:engine/view/attributeelement~AttributeElement} under
     * the {@link module:engine/view/document~Document editing view's} selection or `null`
     * if there is none.
     *
     * **Note**: For a non–collapsed selection the link element is only returned when **fully**
     * selected and the **only** element within the selection boundaries.
     *
     * @private
     * @returns {module:engine/view/attributeelement~AttributeElement|null}
     */
    _getSelectedLinkElement() {
        let target =  super._getSelectedLinkElement();

        if (!target) {
            const nodeAfter = this.editor.editing.view.document.selection
                .getFirstRange().getTrimmed().start.nodeAfter;

            if (nodeAfter && nodeAfter.name === 'figure' && nodeAfter.hasAttribute('linkHref')) {
                target = nodeAfter;

                // const view = this.editor.editing.view;
                // const selection = view.document.selection;
                // const range = selection.getFirstRange().getTrimmed();
                // selection.collapse();
            }
        }

        return target;
    }
}
