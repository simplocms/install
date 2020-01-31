const options = window.viewModuleOptions();

import VariableField from './variable-field';

new Vue({
    el: '#model-view-configuration-form',

    data: {
        localization: new Localization(window.cms_trans),
        formData: {
            view: options.model.view || '',
            variables: options.variables ? { ...options.variables } : {}
        },
        fields: [],
        $form: null,
        previousView: null
    },

    components: {
        'variable-field': VariableField
    },

    computed: {
        hasViews () {
            const views = options.views;
            return Array.isArray(views) ? views.length : Object.keys(views).length;
        }
    },

    methods: {
        /**
         * Load variables for selected view from the server.
         */
        loadVariables () {
            if (this.formData.view === '' || !this.hasViews || !this.$form.lock()) {
                return;
            }

            if (this.previousView && this.previousView !== this.formData.view) {
                this.fields = [];
                this.formData.variables = {};
            }

            axios.get(options.variablesUri, { params: { view: this.formData.view } })
                .then(response => {
                    this.setFields(response.data.fields);
                    this.previousView = this.formData.view;
                })
                .finally(() => {
                    this.$form.unlock();
                });
        },

        /**
         * Set loaded fields + initialize variables.
         * @param {Object[]} fields
         */
        setFields (fields) {
            this.fields = [];
            const variables = {};
            let loadCKEditor = false;

            for (const nameOrIndex in fields) {
                const field = fields[nameOrIndex];

                if (typeof this.formData.variables[field.name] !== 'undefined') {
                    variables[field.name] = this.formData.variables[field.name];
                } else {
                    variables[field.name] = typeof field.value === 'undefined' ? null : field.value;
                }

                if (field.type === 'ckeditor') {
                    loadCKEditor = true;
                }

                this.fields.push(field);
            }

            if (loadCKEditor) {
                this.loadCKEditor();
            }

            this.formData.variables = variables;
        },

        /**
         * Fill form with data from GridEdtior.
         * @param {Object} data
         */
        fillForm (data) {
            this.formData = data;

            for (const vi in data._temp || {}) {
                this.formData.variables[vi] = data._temp[vi];
            }
        },

        /**
         * Give form data to GridEditor.
         * @param {Event} event
         * @param {Object} output - shared object
         */
        getFormData (event, output) {
            output.view = this.formData.view;
            output.variables = {};

            // Get only ids from MediaFiles
            for (const vi in this.formData.variables) {
                const variable = this.formData.variables[vi];
                if (variable && typeof variable.getId === 'function') {
                    output.variables[vi] = variable.getId();
                    output._temp = output._temp || {};
                    output._temp[vi] = variable;
                } else {
                    output.variables[vi] = variable;
                }
            }
        },

        /**
         * Load CKEditor script and initialize CKEditor in fields.
         */
        loadCKEditor () {
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
            script.src = options.CKEditorUri;
            document.head.appendChild(script);

            if (window.cms_locale !== 'en') {
                const CKlocale = document.createElement('script');
                CKlocale.src = `/js/localizations/ckeditor/${window.cms_locale}.js`;
                document.head.appendChild(CKlocale);
            }
        }
    },

    mounted () {
        this.$form = $(this.$el);
        // Form fill
        this.$form.trigger('admin:form-fill-ready', this.fillForm);
        this.$form.on('admin:form-submit-data', this.getFormData);

        // Load variables of the view
        this.loadVariables();
    },

    beforeDestroy () {
        this.$form.off('admin:form-submit-data', this.getFormData);
    },
});
