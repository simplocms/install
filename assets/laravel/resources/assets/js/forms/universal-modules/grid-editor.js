const options = window.universalModuleOptions();
import Form from '../../vendor/Form';
import Multiselect from '../../vue-components/form/multiselect';

new Vue({
    el: '#universal-module-' + options.model.prefix + '-form',

    data: {
        form: new Form({...options.model}),
        $form: null,
        $itemsSelect: null,
        $viewSelect: null,
        localization: new Localization(window.cms_trans)
    },

    components: {
        Multiselect
    },

    mounted: function () {
        this.$form = $(this.$el);

        // Form fill
        this.$form.trigger('admin:form-fill-ready', this.fillForm);
        this.$form.on('admin:form-submit-data', this.getFormData);
        this.$form.on('admin:form-submit-error', this.setFormErrors);
    },

    beforeDestroy() {
        this.$form.off('admin:form-submit-data', this.getFormData);
    },

    methods: {
        fillForm(data) {
            this.form = new Form({...data});
        },

        getFormData(event, output) {
            output.view = this.form.view;
            output.items = [...this.form.items];
            output.all_items = this.form.all_items;
        },

        setFormErrors(event, errors) {
            event.preventDefault();
            this.$nextTick(() => {
                this.form.setErrors(errors);
            });
        }
    }
});
