<template>
    <div tabindex="-1"
         role="dialog"
         :id="id"
         class="modal fade"
         ref="modal"
    >
        <div role="document" class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" @click.prevent="hide" class="close"><span>×</span></button>
                    <h4 class="modal-title" v-if="module">{{ module.title }}</h4>
                </div>

                <div class="modal-body" style="min-height: 200px">
                    <div class="element-lock-hover" v-show="!isLoaded">
                        <div class="lock-inner"><i class="fa fa-spinner fa-spin"></i></div>
                    </div>
                    <div ref="form"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-link" @click.prevent="hide">
                        {{ localization.trans('settings_modal.btn_cancel') }}
                    </button>
                    <button type="button"
                            class="btn btn-primary"
                            :disabled="!isLoaded"
                            @click.prevent="submitForm"
                    >
                        {{ localization.trans('settings_modal.btn_save') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                id: 'module-form-modal',
                module: null,
                isLoaded: false,
                submitCallback: null,
                $content: null,
                $form: null,
            }
        },

        props: {
            formUri: String,
            formUniversalUri: String,
            validateUri: String,
            validateUniversalUri: String,
            localization: Object
        },

        methods: {
            show(module, callback) {
                this.module = module;
                this.submitCallback = callback;
                this.loadForm();
                $(this.$refs.modal).modal('show');
            },

            loadForm() {
                this.isLoaded = false;

                Request.create(this.formUrl, {
                    dataType: 'html',
                    type: 'get',
                    contentType: 'application/json'
                }).done(response => {
                    this.isLoaded = true;
                    this.initializeForm(response);
                });
            },

            initializeForm(html) {
                let filled = false;
                this.$form = null;

                // Fill form with configuration data, if configuration is available.
                if (this.module.configuration) {
                    // Here we use event bubbling, so we catch the event on body element,
                    // otherwise it can fire event before we get form element
                    $('body').one('admin:form-fill-ready', (event, customFiller) => {
                        if (customFiller) {
                            filled = true;
                            customFiller(this.module.configuration);
                        } else if (this.$form) {
                            filled = true;
                            this.fillConfigurationToForm();
                        }
                    });
                }

                this.$content.html($.parseHTML(html, document, true));
                this.$form = this.$content.find('form').addClass('module-configuration-form');

                // Fill form with configuration data, if configuration is available.
                if (this.module.configuration && !filled) {
                    this.fillConfigurationToForm();
                }

                // Submit module configuration form.
                this.$form.on('submit', this.submitForm);
            },

            submitForm(event) {
                event.preventDefault();

                this.$form.trigger('admin:before-form-submit');

                if (!this.module.universal) {
                    Form.removeAllErrors(this.$form);
                }
                if (!this.$form.lock({Spinner: SpinnerType.OVER})) {
                    return;
                }

                const formData = this.getFormData();
                formData.entity_module_name = this.module.name;

                this.saveFormData(formData);
            },

            /**
             * Fill data from configuration to module form.
             */
            fillConfigurationToForm() {
                const configuration = this.module.configuration;

                this.$content.find(':input').val(function (index, value) {
                    if (this.type === 'checkbox') {
                        this.checked = this.value === configuration[this.name];
                    }

                    return configuration[this.name] || value;
                });
            },

            /**
             * Get form input data as an object.
             * @return {object} - configuration of the module
             */
            getFormData() {
                let data = {};
                this.$form.trigger('admin:form-submit-data', data);

                if (data._form) {
                    return data._form;
                }

                if (Object.keys(data).length) {
                    return data;
                }

                data = this.$form.serializeArray();
                const output = {};

                $.map(data, function (n, i) {
                    const regex = /(\w+)\[(\w*)\]/g;
                    const match = regex.exec(n['name']);

                    if (match !== null) {
                        const name = match[1];
                        const key = match[2];

                        if (output[name]) {
                            if (key.length) {
                                output[name][key] = n['value'];
                            } else {
                                output[name].push(n['value']);
                            }
                        } else {
                            if (key.length) {
                                output[name] = {};
                                output[name][key] = n['value'];
                            } else {
                                output[name] = [n['value']];
                            }
                        }
                    } else {
                        output[n['name']] = n['value'];
                    }

                });

                return output;
            },

            saveFormData(data) {
                const {_temp, ...sendData} = data;

                Request.create(this.validateUrl, {
                    dataType: 'json',
                    type: 'post',
                    data: sendData
                })
                    .done(response => {
                        this.hide();

                        delete sendData._token;
                        delete sendData.entity_module_name;

                        if (_temp) {
                            sendData._temp = _temp;
                        }

                        this.module.preview = response.content;
                        this.module.configuration = sendData;

                        this.submitCallback(this.module);
                    })
                    .fail((jqXhr, textStatus) => {
                        if (jqXhr.status === 422) {
                            const event = $.Event('admin:form-submit-error');
                            this.$form.trigger(event, jqXhr.responseJSON.errors);
                            if (!event.isDefaultPrevented()) {
                                Form.addErrors(this.$form, jqXhr.responseJSON.errors);
                            }
                        } else {
                            alert(textStatus);
                        }
                    })
                    .always(() => {
                        this.$form.unlock();
                    });
            },

            hide() {
                $(this.$refs.modal).modal('hide');
            },

            /**
             * BS modal enforces focus, so it steals focus from CK editor popups. This will fix it.
             */
            fixBsModalWithCKEditor() {
                $.fn.modal.Constructor.prototype.enforceFocus = function () {
                    const self = this;
                    $(document)
                        .off('focusin.bs.modal') // guard against infinite focus loop
                        .on('focusin.bs.modal', $.proxy(function (e) {
                            if (document !== e.target &&
                                self.$element[0] !== e.target &&
                                !self.$element.has(e.target).length &&
                                $(e.target).closest('.cke_editor_mt-content-input_dialog') === 0
                            ) {
                                self.$element.trigger('focus');
                            }
                        }, self));
                };
            }
        },

        computed: {
            /**
             * Url to load form of the module.
             * @return {string}
             */
            formUrl() {
                if (this.isEditing) {
                    const uriBase = this.module.universal ? this.formUniversalUri : this.formUri;
                    return uriBase + "/" + this.module.entity_id;
                }

                return this.module.url;
            },

            /**
             * Get url to submit form to validate configuration of the module.
             * @return {string}
             */
            validateUrl() {
                return this.module.universal ? this.validateUniversalUri : this.validateUri;
            },

            /**
             * Is editing module?
             * @returns {boolean}
             */
            isEditing() {
                return Boolean(this.module && this.module.entity_id);
            }
        },

        mounted() {
            this.$content = $(this.$refs.form);
            this.$root.$on('show::' + this.id, this.show);
            this.fixBsModalWithCKEditor();
        },

        destroyed() {
            this.$root.$off('show::' + this.id, this.show);
        }
    }
</script>
