import targetIcon from './external-link-square-alt.svg';
import LinkActionsView from "@ckeditor/ckeditor5-link/src/ui/linkactionsview";

const label = {
    en: 'Otevírat v novém panelu',
    cs: 'Opens in new tab'
};

export default class ExtendedLinkActionsView extends LinkActionsView {
    /**
     * @inheritDoc
     */
    constructor (locale) {
        super(locale);

        /**
         * The edit link button view.
         *
         * @member {module: @ckeditor/ckeditor5-ui/src/button/buttonview~ButtonView}
         */
        this.targetButtonView = this._createButton( label[locale.language] || label.en, targetIcon, 'change_link_target' );

        this.setTemplate( {
            tag: 'div',

            attributes: {
                class: [
                    'ck',
                    'ck-link-actions'
                ],

                // https://github.com/ckeditor/ckeditor5-link/issues/90
                tabindex: '-1'
            },

            children: [
                this.previewButtonView,
                this.editButtonView,
                this.unlinkButtonView,
                this.targetButtonView,
            ]
        } );

    }
}
