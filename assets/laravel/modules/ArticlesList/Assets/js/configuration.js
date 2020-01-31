import Form from "#/js/vendor/Form";
import vSelect from 'vue-select';
import Multiselect from '#/js/vue-components/form/multiselect';

const options = window.articlesListModuleOptions();

Vue.component('v-module-list-articles-select', vSelect);

new Vue({
    el: '#model-articleslist-configuration-form',

    data: {
        localization: new Localization({...window.cms_trans, ...options.trans}),
        form: new Form(options.model),
        $form: null,
    },

    components: {
        Multiselect
    },

    computed: {
        hasViews() {
            const views = options.views;
            return Array.isArray(views) ? views.length : Object.keys(views).length;
        },

        categoriesOptions() {
            return options.categories[this.form.flag_id] || [];
        },

        flagsOptions() {
            return options.flags;
        }
    },

    methods: {
        /**
         * Set loaded fields + initialize variables.
         * @param {Object[]} fields
         */
        setFields(fields) {
            this.fields = [];

        },

        /**
         * Fill form with data from GridEdtior.
         * @param {Object} data
         */
        fillForm(data) {
            this.form = new Form({...data});
        },

        /**
         * Give form data to GridEditor.
         * @param {Event} event
         * @param {Object} output - shared object
         */
        getFormData(event, output) {
            output._form = this.form.getData();
        },
    },

    mounted() {
        this.$form = $(this.$el);
        // Form fill
        this.$form.trigger('admin:form-fill-ready', this.fillForm);
        this.$form.on('admin:form-submit-data', this.getFormData);
    },

    beforeDestroy() {
        this.$form.off('admin:form-submit-data', this.getFormData);
    },
});
