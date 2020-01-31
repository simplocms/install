import LocalizationMixin from '../vue-mixins/localization';
const options = window.pageThemeConfigOptions();

Vue.component('page-theme-config', {
    mixins: [LocalizationMixin],

    mounted () {
        const self = this;
        const $form = $('#theme-config-form');

        $form.find(':input').on('change', function (e) {
            const $input = $(this);

            self.saveValue({
                key: $input.attr('name'),
                value: $input.val()
            });
        });
    },

    methods: {
        saveValue(params) {
            axios.post(options.submitUrl, params)
                .then(response => {
                    if (response.data.error) {
                        window.location.reload(true);
                    } else {
                        $.jGrowl(this.localization.trans('notifications.settings_updated'), {
                            header: this.$root.localization.trans('flash_level.success'),
                            theme: ' bg-teal  alert-styled-left alert-styled-custom-success'
                        });
                    }
                })
                .catch(error => {
                    if (error.response.status === 422) {
                        Form.addErrors($form, error.response.data.errors);
                    }

                    $.jGrowl(this.$root.localization.trans('notifications.validation_failed'), {
                        header: this.$root.localization.trans('flash_level.danger'),
                        theme: 'bg-danger'
                    });
                });
        }
    }
});
