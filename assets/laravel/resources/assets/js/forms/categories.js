import SeoInputs from '../vue-components/form/seo-inputs';
import OpenGraphInputs from '../vue-components/form/open-graph-inputs';
import Form from '../vendor/Form';

Vue.component('categories-form', {
    data() {
        return {
            form: new Form({...this.category}),
        };
    },

    components: {
        'seo-inputs': SeoInputs,
        'open-graph-inputs': OpenGraphInputs,
    },

    props: {
        category: {
            type: Object,
            required: true
        },
    },

    /**
     * On page is ready
     */
    mounted() {
        // Maxlength
        $('.maxlength').maxlength({
            alwaysShow: true
        });
    },

    methods: {
        /**
         * Fired when title is changed.
         * @param {Event} $event
         */
        onNameChanged($event) {
            this.form.name = $event.target.value;

            if (this.form.url === null || !this.form.url.length) {
                this.form.url = this.form.name;
            }
        },

        /**
         * Fired when url is changed.
         * @param {Event} $event
         */
        onUrlChanged($event) {
            this.form.url = $event.target.value;
        },
    },

    watch: {
        'form.url'(newUrl) {
            this.form.url = Converter.removeDiacritics(newUrl);
        }
    }
});
