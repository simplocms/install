<template>
    <div tabindex="-1"
         role="dialog"
         :id="id"
         class="modal fade"
         ref="modal"
    >
        <div role="document" class="modal-dialog">
            <div class="modal-content modal-lg">
                <div class="modal-header">
                    <button type="button" @click.prevent="hide" class="close"><span>×</span></button>
                    <h4 class="modal-title">{{ localization.trans('column_settings_modal.title') }}</h4>
                </div>

                <div class="modal-body">
                    <div class="col-md-6">
                        <fieldset class="content-group">
                            <legend class="text-bold">
                                <i class="fa fa-window-minimize"></i>
                                {{ localization.trans('column_settings_modal.legend') }}
                            </legend>

                            <!-- Class -->
                            <div class="form-group">
                                <label for="input-column-class">
                                    {{ localization.trans('column_settings_modal.labels.class') }}
                                </label>
                                <input id="input-column-class" type="text" class="form-control" v-model="form.class">
                            </div>

                            <!-- ID -->
                            <div class="form-group">
                                <label for="input-column-id">
                                    {{ localization.trans('column_settings_modal.labels.id') }}
                                </label>
                                <input id="input-column-id" type="text" class="form-control" v-model="form.id">
                            </div>

                            <!-- Tags -->
                            <div class="form-group">
                                <label for="input-column-tag">
                                    {{ localization.trans('column_settings_modal.labels.tag') }}
                                </label>
                                <select id="input-column-tag" v-model="form.tag" class="form-control">
                                    <option v-for="tag in allowedTags" :value="tag">{{ tag }}</option>
                                </select>
                            </div>

                            <!-- Background color-->
                            <div class="form-group">
                                <label for="input-column-bg">
                                    {{ localization.trans('column_settings_modal.labels.bg') }}
                                </label>
                                <input id="input-column-bg" type="hidden">
                            </div>

                            <!-- Deactivate -->
                            <div class="form-group">
                                <div class="checkbox">
                                    <label>
                                        <input id="input-column-active"
                                               type="checkbox"
                                               class="styled"
                                        >
                                        {{ localization.trans('column_settings_modal.labels.active') }}
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <!-- Attributes -->
                    <div class="col-md-6">
                        <settings-attributes :attributes="form.attributes"
                                             :localization="localization"
                                             ref="attributes"
                        ></settings-attributes>
                    </div>

                    <div class="clearfix"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-link" @click.prevent="hide">
                        {{ localization.trans('settings_modal.btn_cancel') }}
                    </button>
                    <button type="button" class="btn btn-primary" @click.prevent="submit">
                        {{ localization.trans('settings_modal.btn_save') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import SettingsAttributes from './_settings-attributes';

    const DEFAULTS = {
        class: '',
        id: '',
        tag: 'div',
        bg: null,
        active: true,
        attributes: []
    };

    export default {
        data() {
            return {
                id: 'column-settings-modal',
                form: {...DEFAULTS},
                submitCallback: null,
                formInputs: {
                    $bg: null,
                    $active: null,
                }
            }
        },

        components: {
            'settings-attributes': SettingsAttributes
        },

        props: {
            allowedTags: Array,
            localization: Object
        },

        methods: {
            show(column, submit) {
                // Initialize form fields
                for (const field in DEFAULTS) {
                    if (Object.hasOwnProperty.call(column, field)) {
                        this.form[field] = column[field];
                    } else {
                        this.form[field] = DEFAULTS[field];
                    }
                }

                // Initialize inputs
                this.initializeInputs();

                this.submitCallback = submit;
                $(this.$refs.modal).modal('show');
            },

            submit() {
                this.submitCallback({
                    ...this.form,
                    wrapper: this.showWrapper ? this.aceEditor.getValue() || null : null,
                    attributes: this.$refs.attributes.getAttributes()
                });
                this.hide();
            },

            hide() {
                $(this.$refs.modal).modal('hide');
            },

            initializeInputs() {
                this.formInputs.$bg.colorPicker('changeColor', this.form.bg);
                this.formInputs.$active.prop('checked', this.form.active === false).uniform();
            },
        },

        mounted() {
            this.$root.$on('show::' + this.id, this.show);

            this.formInputs.$bg = $('#input-column-bg');
            this.formInputs.$bg.on('change', (e, value) => {
                this.form.bg = value || null;
            }).colorPicker();

            this.formInputs.$active = $('#input-column-active');
            this.formInputs.$active.on('change', event => {
                this.form.active = !event.target.checked;
            });
        },

        destroyed() {
            this.$root.$off('show::' + this.id, this.show);
        }
    }
</script>
