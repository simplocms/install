import Photogallery from '../vue-components/photogallery';
import DatePicker from '../vue-components/date-picker';
import TimePicker from '../vue-components/time-picker';
import SeoInputs from '../vue-components/form/seo-inputs';
import OpenGraphInputs from '../vue-components/form/open-graph-inputs';
import Form from '../vendor/Form';

Vue.component('photogalleries-form', {
    data() {
        return {
            form: new Form({
                ...this.photogallery,
            }).addDataCollector(this.getFormData),
            ckEditor: null,
        };
    },

    props: {
        photogallery: {
            type: Object,
            required: true
        }
    },

    components: {
        'photogallery': Photogallery,
        'date-picker': DatePicker,
        'time-picker': TimePicker,
        'seo-inputs': SeoInputs,
        'open-graph-inputs': OpenGraphInputs,
    },

    mounted() {
        // Maxlength
        $('.maxlength').maxlength({
            alwaysShow: true
        });

        // CK Editor
        ClassicEditor.create(document.querySelector('#editor-full'))
            .then(editor => this.ckEditor = editor);
    },

    methods: {
        /**
         * Fired when title is changed.
         * @param {Event} $event
         */
        onTitleChanged($event) {
            this.form.title = $event.target.value;

            if (this.form.url === null || !this.form.url.length) {
                this.form.url = this.form.title;
            }
        },

        /**
         * Fired when url is changed.
         * @param {Event} $event
         */
        onUrlChanged($event) {
            this.form.url = $event.target.value;
        },

        getFormData() {
            return {
                text: this.ckEditor.getData(),
                photogallery: this.$refs.photogallery.getFormData()
            };
        }
    },

    watch: {
        'form.url'(newUrl) {
            this.form.url = Converter.removeDiacritics(newUrl);
        }
    }

});
