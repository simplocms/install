import Form from '../vendor/Form';

Vue.component('settings-form', {
    data() {
        return {
            form: new Form({...this.settings}),
        };
    },

    props: {
        settings: {
            type: Object,
            required: true
        }
    },

    methods: {
        onSaved(response) {
            $.jGrowl(response.data.message, {
                header: this.$root.localization.trans('flash_level.success'), theme: 'bg-teal'
            });
        },

        openThemeModal() {
            $(this.$refs.themeModal).modal('show');
        },

        changeTemplate($event) {
            axios.post($event.target.href)
                .then(() => {
                    window.location.reload();
                })
                .catch(() => {
                    window.location.reload();
                });
        }
    },

    watch: {
        'form.search_uri'(newUrl) {
            this.form.search_uri = Converter.removeDiacritics(this.form.search_uri);
        }
    }

});
