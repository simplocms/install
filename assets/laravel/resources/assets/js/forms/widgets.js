import Form from "../vendor/Form";

Vue.component('widgets-form', {
    data () {
        return {
            form: new Form({
                ...this.widget,
                language_id: this.languageId
            }).addDataCollector(this.getFormData),
        };
    },

    props: {
        widget: {
            type: Object,
            required: true
        },
        languageId: {
            type: Number,
            required: true
        }
    },

    methods: {
        getFormData() {
            return this.$refs.gridEditor.getFormData();
        },
    },
});
