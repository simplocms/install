import Multiselect from '../vue-components/form/multiselect';

export default {
    props: {
        field: {
            type: Object,
            required: true
        },
        value: {}
    },

    data() {
        return {
            /** @type VNode */
            inputNode: null,
            ckEditor: null
        };
    },

    computed: {
        isCKEditor() {
            return this.field.type === 'ckeditor';
        },

        isTextArea() {
            return this.field.type === 'textarea';
        },

        isSelect() {
            return this.field.type === 'select';
        },

        isInput() {
            return !this.isSelect && !this.isTextArea && !this.isCKEditor && !this.isMediaFile;
        },

        isMediaFile() {
            return this.field.type === 'media_file' || this.field.type === 'image';
        },

        isCheckbox() {
            return this.field.type === 'checkbox';
        },

        isCheckboxSwitch() {
            return this.field.type === 'checkbox_switch';
        },

        commonFieldBehaviour() {
            return {
                domProps: {
                    value: this.value
                },
                on: {
                    input: event => {
                        this.$emit('input', event.target.value);
                    }
                },
                ref: 'field'
            };
        },

        commonFieldAttrs() {
            return {
                required: this.field.required || false,
                class: 'form-control',
                name: this.fieldName
            };
        },

        fieldName() {
            return this.field.name;
        },

        hasLabel() {
            return this.field.label && !this.isCheckbox && !this.isCheckboxSwitch;
        }
    },

    methods: {
        /**
         * Create input field for specific type of the field.
         * @param {Function} createElement
         * @returns {Object}
         */
        createFieldElement(createElement) {
            switch (true) {
                case this.isCKEditor:
                case this.isTextArea:
                    return this.createTextAreaElement(createElement);
                case this.isSelect:
                    return this.createSelectElement(createElement);
                case this.isCheckbox:
                    return this.createCheckboxElement(createElement);
                case this.isCheckboxSwitch:
                    return this.createCheckboxSwitchElement(createElement);
                case this.isInput:
                    return this.createInputElement(createElement);
                case this.isMediaFile:
                    return this.createFileSelectorElement(createElement);
            }
        },

        /**
         * Create input element.
         * @param {Function} createElement
         * @returns {Object}
         */
        createInputElement(createElement) {
            return createElement('input', {
                attrs: {
                    ...this.commonFieldAttrs,
                    type: this.field.type || 'text',
                    placeholder: this.field.placeholder || null,
                    maxlength: this.field.maxlength || null,
                    max: this.field.max || null,
                    min: this.field.min || null,
                    step: this.field.step || null,
                },
                ...this.commonFieldBehaviour
            });
        },

        /**
         * Create select element.
         * @param {Function} createElement
         * @returns {Object}
         */
        createSelectElement(createElement) {
            const options = [];

            const commonBehaviour = this.commonFieldBehaviour;

            if (this.field.multiple) {

                commonBehaviour.on.input = values => this.$emit('input', values);

                return createElement(
                    Multiselect,
                    {
                        attrs: {
                            allowEmpty: !this.field.required,
                            name: this.fieldName,
                            multiple: true,
                            label:"name"
                        },
                        props: {
                            options: this.field.options,
                            value: this.value
                        },
                        ...commonBehaviour
                    }
                );
            }

            for (const value in this.field.options || []) {
                options.push(
                    createElement(
                        'option', {
                            attrs: {
                                value: value,
                            },
                            domProps: {
                                selected: value == this.value // will come here only for single select
                            }
                        },
                        this.field.options[value]
                    )
                )
            }

            return createElement(
                'select',
                {
                    attrs: {
                        ...this.commonFieldAttrs,
                        multiple: this.field.multiple || false,
                    },
                    ...commonBehaviour
                },
                options
            );
        },

        /**
         * Create textarea element.
         * @param {Function} createElement
         * @returns {Object}
         */
        createTextAreaElement(createElement) {
            return createElement('textarea', {
                attrs: {
                    ...this.commonFieldAttrs,
                    placeholder: this.field.placeholder || null,
                    maxlength: this.field.maxlength || null,
                    rows: this.field.rows || null
                },
                ...this.commonFieldBehaviour
            });
        },

        /**
         * Create checkbox element.
         * @param {Function} createElement
         * @returns {Object}
         */
        createCheckboxElement(createElement) {
            return createElement('div', {
                attrs: {
                    class: 'checkbox',
                },
            }, [
                createElement('label', [
                    createElement('input', {
                        attrs: {
                            ...this.commonFieldAttrs,
                            type: 'checkbox',
                            'class': null
                        },
                        ...this.commonFieldBehaviour,
                        domProps: {
                            checked: Boolean(this.value)
                        },
                        on: {
                            input: event => {
                                this.$emit('input', event.target.checked);
                            }
                        },
                    }),
                    this.field.label
                ]),
            ]);
        },

        /**
         * Create checkbox switch element.
         * @param {Function} createElement
         * @returns {Object}
         */
        createCheckboxSwitchElement(createElement) {
            return createElement('v-checkbox-switch', {
                props: {
                    value: this.value,
                    'name': this.fieldName,
                },
                on: {
                    input: event => {
                        this.$emit('input', event);
                    }
                },
            }, [
                this.field.label
            ]);
        },

        /**
         * Create file selector element.
         * @param {Function} createElement
         * @returns {Object}
         */
        createFileSelectorElement(createElement) {
            return createElement('media-library-file-selector', {
                props: {
                    'image': this.field.type === 'image',
                    value: this.value,
                    'input-name': this.fieldName,
                    'file-type': this.field.fileType || null,
                },
                on: {
                    input: file => {
                        this.$emit('input', file);
                    }
                }
            });
        },

        /**
         * Initialize CKEditor.
         * This method is called from mounted life-cycle hook and from parent, when CKEditor is loaded.
         */
        initializeCKEditor() {
            if (this.isCKEditor && window.CKEDITOR_READY) {
                ClassicEditor.create(this.$refs.field)
                    .then(editor => {
                        this.ckEditor = editor;
                        editor.model.document.on('change:data', () => {
                            this.$emit('input', editor.getData());
                        });
                    });
            }
        }
    },

    render(createElement) {
        return createElement('div', {attrs: {class: 'form-group' + (this.field.required ? ' required' : '')}}, [
            // label
            this.hasLabel ? createElement('label', [this.field.label]) : null,
            // input
            this.createFieldElement(createElement),
            // error
            createElement('span', {attrs: {class: 'help-block'}})
        ])
    },

    mounted() {
        this.initializeCKEditor();
    },

    beforeDestroy() {
        if (this.ckEditor) {
            this.ckEditor.destroy();
        }
    }
};
