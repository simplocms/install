import LocalizationMixin from '../vue-mixins/localization';

Vue.component('page-modules', {
    mixins: [LocalizationMixin],

    mounted() {
        const self = this;

        $('.action-uninstall').click(function (event) {
            event.preventDefault();
            self.uninstall($(this));
        });
    },

    methods: {
        uninstall($target) {
            swal({
                title: this.localization.trans('confirm_uninstall.title'),
                text: this.localization.trans('confirm_uninstall.text'),
                icon: "warning",
                buttons: {
                    cancel: {
                        text: this.localization.trans('confirm_uninstall.cancel'),
                        visible: true
                    },
                    confirm: {
                        text: this.localization.trans('confirm_uninstall.confirm'),
                        value: true
                    }
                },
                dangerMode: true
            })
                .then(isConfirm => {
                    if (!isConfirm) return;

                    $target.lock();
                    axios.post($target.attr('href'))
                        .then(response => {
                            if (response.data.refresh) {
                                location.reload();
                            } else if (response.data.error) {
                                $.jGrowl(response.data.error, {
                                    header: this.$root.localization.trans('flash_level.danger'),
                                    theme: ' bg-danger  alert-styled-left alert-styled-custom-danger'
                                });
                            }
                            $target.unlock();
                        })
                        .catch(() => {
                            $target.unlock();
                        });
                });
        }
    }
});
