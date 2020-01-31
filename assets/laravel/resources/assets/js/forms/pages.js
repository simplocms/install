import DatePicker from '../vue-components/date-picker';
import TimePicker from '../vue-components/time-picker';
import SeoInputs from '../vue-components/form/seo-inputs';
import Multiselect from '../vue-components/form/multiselect';
import OpenGraphInputs from '../vue-components/form/open-graph-inputs';
import BinarySwitch from '../vue-components/form/binary-switch';
import Form from '../vendor/Form';
import LocalizationMixin from '../vue-mixins/localization';

Vue.component('pages-form', {
    mixins: [LocalizationMixin],

    data() {
        return {
            form: new Form({
                ...this.page,
                set_unpublish_at: this.page.unpublish_at_date !== null,
            }).addDataCollector(this.getFormData),
            isSaving: false,
            testing: this.testingCounterpart !== null ? {
                activeVariantId: this.activeTestingVariantId,
                form: new Form({
                    ...this.page,
                    ...this.testingCounterpart.defaultValues,
                }).addDataCollector(this.getFormData),
                versionSwitchUrl: this.testingCounterpart.versionSwitchUrl,
                versions: this.testingCounterpart.versions,
                content: this.testingCounterpart.content,
                submitUrl: this.testingCounterpart.submitUrl,
            } : null,
            activeTab: 0,
            innerSubmitUrl: this.submitUrl
        };
    },

    props: {
        page: {
            type: Object,
            required: true
        },
        submitUrl: {
            type: String,
            required: true
        },
        parentPages: null,
        activeTestingVariantId: Number,
        testingCounterpart: {
            type: Object,
            default: null
        },
    },

    components: {
        'date-picker': DatePicker,
        'time-picker': TimePicker,
        'seo-inputs': SeoInputs,
        'open-graph-inputs': OpenGraphInputs,
        Multiselect,
        BinarySwitch
    },

    computed: {
        isTestingCounterpart() {
            return this.testingCounterpart !== null && Number(this.page.id) !== this.testing.activeVariantId;
        }
    },

    methods: {

        /**
         * Triggered when form is successfully submitted.
         * @param {object} response
         */
        onSuccess(response) {
            if (this.isSaving) {
                $.jGrowl(response.data.message, {
                    header: this.$root.localization.trans('flash_level.success'), theme: 'bg-teal'
                });

                this.form.url = response.data.url;
                if (response.data.newContent) {
                    this.$refs.gridEditor.setVersions(response.data.versions);
                    this.$refs.gridEditor.setContent(response.data.newContent);
                }

                this.isSaving = false;
                this.form.resetChangeState();
            }
        },

        /**
         * Fired when title is changed.
         * @param {Event} $event
         */
        onNameChanged($event) {
            this.form.name = $event.target.value;

            if (!this.isTestingCounterpart && (this.form.url === null || !this.form.url.length)) {
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

        getFormData(data) {
            if (this.isSaving) {
                data.prevent_redirect = this.isSaving;
            }

            return this.$refs.gridEditor.getFormData();
        },

        switchingTestingVariant(pageId) {
            if (this.testing.activeVariantId === pageId) {
                return;
            }

            if (this.form.hasChanged() && !confirm(this.localization.trans('testing_variant_switch_confirm'))) {
                return;
            }

            this.switchTestingCounterpart(pageId);
        },

        switchTestingCounterpart(pageId) {
            if (this.isTestingCounterpart) {
                this.switchFromTestingCounterpart(pageId);
            } else {
                this.switchToTestingCounterpart(pageId);
            }

            this.testing.activeVariantId = pageId;
        },

        switchToTestingCounterpart() {
            this.testing.backup = {
                activeVersionIndex: this.$refs.gridEditor.activeVersionIndex,
                versionSwitchUrl: this.$refs.gridEditor.urls.switchVersion,
                versions: this.$refs.gridEditor.getVersions(),
                content: this.$refs.gridEditor.getContent(),
                form: this.form,
                submitUrl: this.innerSubmitUrl
            };

            this.setTestingConfig(this.testing);
        },

        switchFromTestingCounterpart() {
            this.testing.activeVersionIndex = this.$refs.gridEditor.activeVersionIndex;
            this.testing.versions = this.$refs.gridEditor.getVersions();
            this.testing.content = this.$refs.gridEditor.getContent();

            this.setTestingConfig(this.testing.backup);
            delete this.testing.backup;
        },

        setTestingConfig(config) {
            this.$refs.gridEditor.urls.switchVersion = config.versionSwitchUrl;
            this.$refs.gridEditor.activeVersionIndex = config.activeVersionIndex || 0;
            this.$refs.gridEditor.setVersions(config.versions);
            this.$refs.gridEditor.setContent(config.content);

            this.form.resetChangeState();
            this.form = config.form;
            this.innerSubmitUrl = config.submitUrl;
        },

        hasUnsavedChanges() {
            return this.form.hasChanged();
        }
    },

    mounted() {
        window.onbeforeunload = () => {
            return this.hasUnsavedChanges() ? '' : null;
        }
    },

    watch: {
        'form.url'(newUrl) {
            this.form.url = Converter.removeDiacritics(newUrl);
        },
    }
});
