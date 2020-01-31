import Plugin from '@ckeditor/ckeditor5-core/src/plugin';
import imageIcon from '@ckeditor/ckeditor5-core/theme/icons/image.svg';
import ButtonView from '@ckeditor/ckeditor5-ui/src/button/buttonview';

const label = {
    en: 'Insert image',
    cs: 'Vložit obrázek',
};

export default class MediaLibrary extends Plugin {
    init() {
        const editor = this.editor;

        editor.ui.componentFactory.add('mediaLibrary', locale => {
            const view = new ButtonView(locale);

            view.set({
                label: label[editor.locale.language] || label.en,
                icon: imageIcon,
                tooltip: true
            });

            // Callback executed once the image is clicked.
            view.on('execute', () => {
                window.MediaLibraryPrompt.singleImage()
                    .open()
                    .then(image => {
                        editor.model.change(writer => {
                            const imageElement = writer.createElement('image', {
                                src: image.getUrl(),
                                alt: image.getDescription() || image.getUrl()
                            });
                            editor.model.insertContent(imageElement, editor.model.document.selection);
                        });
                    })
                    .catch(() => {
                        // do nothing
                    });
            });

            return view;
        });
    }
}
