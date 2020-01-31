import Field from './field';
import Form from "../../vendor/Form";
import SeoInputs from '../../vue-components/form/seo-inputs';
import OpenGraphInputs from '../../vue-components/form/open-graph-inputs';

Vue.component('universalmodule-form', {
    data() {
        return {
            form: new Form({
                fields: {...this.content},
                ...this.item
            }).addDataCollector(this.getFormData)
        };
    },

    components: {
        'field': Field,
        'seo-inputs': SeoInputs,
        'open-graph-inputs': OpenGraphInputs,
    },

    props: {
        content: {
            type: Object,
            required: true
        },
        item: {
            type: Object,
            required: true
        },
        fields: {
            type: Array,
            required: true,
        },
        order: Number,
        CkEditorUri: String
    },

    methods: {
        /**
         * Get form data.
         * @param {Object} data
         * @returns {Object}
         */
        getFormData (data) {
            const fields = {...data.fields};
            data.fields = null;

            // Files to ids
            for (const name in fields) {
                data[name] = fields[name];
            }

            return data;
        },

        /**
         * Load CKEditor script and initialize CKEditor in fields.
         */
        loadCKEditor() {
            if (typeof window.CKEDITOR_VERSION !== 'undefined') {
                window.CKEDITOR_READY = true;
            }

            if (window.CKEDITOR_READY) {
                return;
            }

            const script = document.createElement('script');
            script.onload = () => {
                window.CKEDITOR_READY = true;
                if (this.$refs.fields) {
                    this.$refs.fields.map(component => component.initializeCKEditor());
                }
            };
            script.src = this.CkEditorUri;
            document.head.appendChild(script);

            if (window.cms_locale !== 'en') {
                const CKlocale = document.createElement('script');
                CKlocale.src = `/js/localizations/ckeditor/${window.cms_locale}.js`;
                document.head.appendChild(CKlocale);
            }
        },

        /**
         * Initializes form data - from content and default field values.
         */
        initializeFormData() {
            const formFields = {};
            let loadCKEditor = false;

            for (const index in this.fields) {
                const field = this.fields[index];

                if (typeof this.content[field.name] !== 'undefined') {
                    formFields[field.name] = this.content[field.name];
                } else {
                    formFields[field.name] = typeof field.value === 'undefined' ? null : field.value;
                }

                if (field.type === 'ckeditor') {
                    loadCKEditor = true;
                }
            }

            if (loadCKEditor) {
                this.loadCKEditor();
            }

            this.form.fields = formFields;
        }
    },

    created() {
        this.initializeFormData();
    }

});
