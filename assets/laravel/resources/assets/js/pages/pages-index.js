import VModal from 'vue-js-modal';
import LocalizationMixin from '../vue-mixins/localization';

Vue.use(VModal, { dialog: true });
Vue.component('pages-index', {
    mixins: [LocalizationMixin],

    methods: {
        stopTesting($event) {
            this.$modal.show('dialog', {
                title: $event.control.text,
                text: this.localization.trans('text'),
                buttons: [
                    {
                        title: this.localization.trans('keep_both'),
                        handler: () => { this.confirmStopTesting($event.control.url, null) }
                    },
                    {
                        title: this.localization.trans('keep_a'),
                        handler: () => { this.confirmStopTesting($event.control.url, 'a') }
                    },
                    {
                        title: this.localization.trans('keep_b'),
                        handler: () => { this.confirmStopTesting($event.control.url, 'b') }
                    },
                ]
            })
        },

        confirmStopTesting(url, keep) {
            axios.post(url, {keep})
                .then(resposne => {
                    this.$modal.hide('dialog');
                    location.reload();
                })
                .catch(thrown => {
                    $.jGrowl(thrown.response.data.message, {
                        header: this.$root.localization.trans('flash_level.danger'),
                        theme: 'bg-danger'
                    });
                })
        }
    },
});
