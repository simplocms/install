import ClassicEditorBase from '@ckeditor/ckeditor5-editor-classic/src/classiceditor';
import EssentialsPlugin from '@ckeditor/ckeditor5-essentials/src/essentials';
import AutoformatPlugin from '@ckeditor/ckeditor5-autoformat/src/autoformat';
import BoldPlugin from '@ckeditor/ckeditor5-basic-styles/src/bold';
import ItalicPlugin from '@ckeditor/ckeditor5-basic-styles/src/italic';
import UnderlinePlugin from '@ckeditor/ckeditor5-basic-styles/src/underline';
import StrikethroughPlugin from '@ckeditor/ckeditor5-basic-styles/src/strikethrough';
import BlockQuotePlugin from '@ckeditor/ckeditor5-block-quote/src/blockquote';
import HeadingPlugin from '@ckeditor/ckeditor5-heading/src/heading';
import ImagePlugin from '@ckeditor/ckeditor5-image/src/image';
import ImageCaptionPlugin from '@ckeditor/ckeditor5-image/src/imagecaption';
import ImageStylePlugin from '@ckeditor/ckeditor5-image/src/imagestyle';
import ImageToolbarPlugin from '@ckeditor/ckeditor5-image/src/imagetoolbar';
import ListPlugin from './extended-list/list';
import ParagraphPlugin from '@ckeditor/ckeditor5-paragraph/src/paragraph';
import Table from '@ckeditor/ckeditor5-table/src/table';
import TableToolbar from '@ckeditor/ckeditor5-table/src/tabletoolbar';
import Alignment from '@ckeditor/ckeditor5-alignment/src/alignment';
import MediaEmbed from '@ckeditor/ckeditor5-media-embed/src/mediaembed';
import Clipboard from '@ckeditor/ckeditor5-clipboard/src/clipboard';
import Enter from '@ckeditor/ckeditor5-enter/src/enter';
import ShiftEnter from '@ckeditor/ckeditor5-enter/src/shiftenter';
import Widget from '@ckeditor/ckeditor5-widget/src/widget';
import ImageLink from './image-link/image-link';

import MediaLibraryPlugin from './media-library-plugin';

import './styles.css'
import ExtendedLinkPlugin from "./extended-link/link";
import NonBreakingSpacePlugin from "./non-breaking-space-plugin";

const customDictionary = {
    cs: {
        heading4: 'Nadpis 4'
    },
    en: {
        heading4: 'Heading 4'
    }
};

const customLocale = customDictionary[window.cms_locale] || customDictionary.en;

export default class ClassicEditor extends ClassicEditorBase {
    constructor(sourceElementOrData, config) {
        super(sourceElementOrData, config);

        if (!window.ck_config) {
            return;
        }

        const css = window.ck_config.css || null;
        const cssId = 'ck-editor-theme-styles';

        if (css && !document.getElementById(cssId)) {
            const style = document.createElement('style');
            style.id = cssId;
            style.type = 'text/css';
            style.innerHTML = css;
            document.getElementsByTagName('head')[0].appendChild(style);
        }
    }
}

ClassicEditor.builtinPlugins = [
    EssentialsPlugin,
    AutoformatPlugin,
    BoldPlugin,
    ItalicPlugin,
    BlockQuotePlugin,
    HeadingPlugin,
    ImagePlugin,
    ImageCaptionPlugin,
    ImageStylePlugin,
    ImageToolbarPlugin,
    ExtendedLinkPlugin,
    ListPlugin,
    ParagraphPlugin,
    MediaLibraryPlugin,
    Table,
    TableToolbar,
    StrikethroughPlugin,
    UnderlinePlugin,
    Alignment,
    MediaEmbed,
    Clipboard,
    Enter,
    ShiftEnter,
    Widget,
    ImageLink,
    NonBreakingSpacePlugin
];

let headingOptions = [
    {model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph'},
    {model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1'},
    {model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2'},
    {model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3'},
    {
        model: 'heading4',
        view: 'h4',
        title: customLocale.heading4,
        class: 'ck-heading_heading4'
    }
];

let listTypes = null;

if (window.ck_config) {
    headingOptions = window.ck_config.heading_options || headingOptions;
    listTypes = window.ck_config.lists || listTypes;
}

ClassicEditor.defaultConfig = {
    toolbar: {
        items: [
            'heading',
            '|',
            'bold',
            'italic',
            'underline',
            'strikethrough',
            '|',
            'alignment',
            '|',
            'bulletedList',
            'numberedList',
            '|',
            'link',
            'blockQuote',
            'mediaLibrary',
            'insertTable',
            'mediaEmbed',
            '|',
            'undo',
            'redo'
        ]
    },
    heading: {
        options: headingOptions
    },
    list: {
        types: listTypes
    },
    mediaEmbed: {
        previewsInData: true
    },
    image: {
        toolbar: [
            'imageTextAlternative', 'link',  '|',
            'imageStyle:full', 'imageStyle:side', '|',
            'imageStyle:alignLeft', 'imageStyle:alignCenter', 'imageStyle:alignRight'
        ],
        styles: [
            'full', 'side', 'alignLeft', 'alignRight', 'alignCenter'
        ]
    },
    table: {
        toolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
    },
    language: window.cms_locale
};

window.ClassicEditor = ClassicEditor;
