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
                    <h4 class="modal-title">{{ localization.trans('container_settings_modal.title') }}</h4>
                </div>

                <div class="modal-body">
                    <div class="col-md-6">
                        <fieldset class="content-group">
                            <legend class="text-bold">
                                <i class="fa fa-object-group"></i>
                                {{ localization.trans('container_settings_modal.legend') }}
                            </legend>

                            <!-- Class -->
                            <div class="form-group">
                                <label for="input-container-class">
                                    {{ localization.trans('container_settings_modal.labels.class') }}
                                </label>
                                <input id="input-container-class" type="text" class="form-control"
                                       v-model="form.class"
                                >
                            </div>

                            <!-- ID -->
                            <div class="form-group">
                                <label for="input-container-id">
                                    {{ localization.trans('container_settings_modal.labels.id') }}
                                </label>
                                <input id="input-container-id" type="text" class="form-control" v-model="form.id">
                            </div>

                            <!-- Tags -->
                            <div class="form-group">
                                <label for="input-container-tag">
                                    {{ localization.trans('container_settings_modal.labels.tag') }}
                                </label>
                                <select id="input-container-tag" v-model="form.tag" class="form-control">
                                    <option v-for="tag in allowedTags" :value="tag">{{ tag }}</option>
                                </select>
                            </div>

                            <!-- Background color-->
                            <div class="form-group">
                                <label for="input-container-bg">
                                    {{ localization.trans('container_settings_modal.labels.bg') }}
                                </label>
                                <input id="input-container-bg" type="hidden">
                            </div>

                            <!-- Fluid -->
                            <div class="form-group">
                                <div class="checkbox">
                                    <label>
                                        <input id="input-container-fluid"
                                               type="checkbox"
                                               class="styled"
                                        >
                                        {{ localization.trans('container_settings_modal.labels.fluid') }}
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="checkbox">
                                    <label>
                                        <input id="input-container-active"
                                               type="checkbox"
                                               class="styled"
                                        >
                                        {{ localization.trans('container_settings_modal.labels.active') }}
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="checkbox">
                                    <label>
                                        <input id="input-container-wrap"
                                               type="checkbox"
                                               class="styled"
                                        >
                                        {{ localization.trans('container_settings_modal.labels.wrap') }}
                                    </label>
                                </div>
                            </div>

                            <div class="form-group" v-show="showWrapper">
                                <p v-html="localization.trans('container_settings_modal.wrap_help_text', {
                                    code: '<code>[container]</code>'
                                })">
                                </p>
                                <div ref="wrapperInput" id="input-container-wrapper"></div>
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
        fluid: false,
        wrapper: '',
        attributes: []
    };

    const WRAPPER_EXAMPLE = `
        <div class="vlastni-trida">
            [container]
        </div>
    `;

    export default {
        data() {
            return {
                id: 'container-settings-modal',
                showWrapper: false,
                form: {...DEFAULTS},
                submitCallback: null,
                formInputs: {
                    $bg: null,
                    $fluid: null,
                    $active: null,
                    $wrap: null,
                },
                aceEditor: null
            }
        },

        props: {
            allowedTags: Array,
            localization: Object
        },

        components: {
            'settings-attributes': SettingsAttributes
        },

        methods: {
            show(container, submit) {
                // Initialize form fields
                for (const field in DEFAULTS) {
                    if (Object.hasOwnProperty.call(container, field)) {
                        this.form[field] = container[field];
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
                this.formInputs.$fluid.prop('checked', this.form.fluid).uniform();
                this.formInputs.$active.prop('checked', this.form.active === false).uniform();
                this.formInputs.$wrap.prop('checked', Boolean(this.form.wrapper)).uniform();

                // wrapper
                this.showWrapper = Boolean(this.form.wrapper);
                if (this.form.wrapper) {
                    this.aceEditor.setValue(html_beautify(this.form.wrapper));
                }
            },

            toggleWrapper(show) {
                if (show && !this.form.wrapper) {
                    this.aceEditor.setValue(html_beautify(WRAPPER_EXAMPLE));
                }

                this.showWrapper = show;
            },
        },

        mounted() {
            this.$root.$on('show::' + this.id, this.show);

            this.formInputs.$bg = $('#input-container-bg');
            this.formInputs.$bg.on('change', (e, value) => {
                this.form.bg = value || null;
            }).colorPicker();

            this.formInputs.$fluid = $('#input-container-fluid');
            this.formInputs.$fluid.on('change', event => {
                this.form.fluid = event.target.checked;
            });

            this.formInputs.$active = $('#input-container-active');
            this.formInputs.$active.on('change', event => {
                this.form.active = !event.target.checked;
            });

            this.formInputs.$wrap = $('#input-container-wrap');
            this.formInputs.$wrap.on('change', event => this.toggleWrapper(event.target.checked));

            // Wrapper
            this.aceEditor = ace.edit(this.$refs.wrapperInput.getAttribute('id'));
            this.aceEditor.setTheme("ace/theme/monokai");
            this.aceEditor.getSession().setMode("ace/mode/html");
            this.aceEditor.setShowPrintMargin(false);
            this.aceEditor.$blockScrolling = Infinity;
            this.aceEditor.setOptions({
                maxLines: 10
            });
        },

        destroyed() {
            this.$root.$off('show::' + this.id, this.show);
        }
    }
</script>
