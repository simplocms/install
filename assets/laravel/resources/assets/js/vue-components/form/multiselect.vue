<script>
    import Multiselect from 'vue-multiselect';
    import 'vue-multiselect/dist/vue-multiselect.min.css';

    export default {
        name: "multiselect",

        inheritAttrs: false,

        data() {
            return {
                innerValue: [],
            }
        },

        props: {
            options: {
                required: true,
                type: [Array, Object]
            },
            value: null,
            trackBy: {
                type: String,
                default: 'value'
            },
            label: {
                type: String,
                default: 'label'
            },
            multiple: Boolean
        },

        render(h) {
            const scopedSlots = this.$vnode.data.scopedSlots;
            const children = Object.keys(this.$slots).map(slot => h('template', {slot}, this.$slots[slot]));

            if (typeof this.$slots.noOptions === 'undefined') {
                children.push(
                    h('template', {slot: 'noOptions'}, [
                        this.$root.localization.trans('components.multiselect.no_options')
                    ])
                );
            }

            if (typeof this.$slots.noResult === 'undefined') {
                children.push(
                    h('template', {slot: 'noResult'}, [
                        this.$root.localization.trans('components.multiselect.no_result')
                    ])
                );
            }

            return h(Multiselect, {
                scopedSlots,
                props: {
                    ...this.locales,
                    ...this.$props,
                    options: this.validOptions,
                    value: this.innerValue,
                    trackBy: this.trackBy,
                    label: this.label,
                    multiple: this.multiple,
                },
                attrs: this.$attrs,
                on: {
                    ...this.listeners,
                    input: this.input,
                }
            }, children)
        },

        methods: {
            input(data) {
                let value;

                if (data === null) {
                    value = null;
                } else if (this.multiple) {
                    value = data.map(item => item[this.trackBy]);
                } else {
                    value = data[this.trackBy];
                }

                this.$emit('input', value);
                this.innerValue = data;
            },

            makeOption(value, label) {
                const option = {};
                option[this.trackBy] = value;
                option[this.label] = label;
                return option;
            },

            fillInnerValue(value) {
                let innerValue = value;

                if (innerValue === null) {
                    this.innerValue = null;
                    return;
                }

                if (this.multiple) {
                    if (!Array.isArray(innerValue)) {
                        innerValue = [innerValue];
                    }

                    this.innerValue = innerValue.map(value => {
                        return this.optionsMap[value];
                    });
                } else {
                    this.innerValue = this.optionsMap[innerValue];
                }

                if (typeof this.innerValue === 'undefined') {
                    this.innerValue = null;
                }
            },

            addMapEntry(map, option) {
                const groupValuesKey = this.$attrs['group-values'];

                if (groupValuesKey && Object.prototype.hasOwnProperty.call(option, groupValuesKey)) {
                    const groupValues = option[groupValuesKey];

                    for (const index in groupValues) {
                        this.addMapEntry(map, groupValues[index]);
                    }
                } else {
                    map[option[this.trackBy]] = option;
                }
            },
        },

        computed: {
            listeners() {
                const {input, ...listeners} = this.$listeners;
                return listeners;
            },

            locales() {
                return {
                    selectLabel: this.$attrs['select-label'] || this.$root.localization.trans('components.multiselect.select_label'),
                    selectedLabel: this.$attrs['selected-label'] || this.$root.localization.trans('components.multiselect.selected_label'),
                    deselectLabel: this.$attrs['deselect-label'] || this.$root.localization.trans('components.multiselect.deselect_label'),
                    placeholder: this.$attrs['placeholder'] || this.$root.localization.trans('components.multiselect.placeholder'),
                };
            },

            optionsMap() {
                const map = {};

                for (const index in this.validOptions) {
                    this.addMapEntry(map, this.validOptions[index]);
                }

                return map;
            },

            validOptions() {
                if (!Array.isArray(this.options)) {
                    const options = [];

                    for (const value in this.options || []) {
                        if (Object.prototype.hasOwnProperty.call(this.options, value)) {
                            options.push(this.makeOption(value, this.options[value]));
                        }
                    }

                    return options;
                }

                return this.options;
            },
        },

        watch: {
            value: {
                handler(newVal) {
                    this.fillInnerValue(newVal);
                },
                immediate: true
            }
        }
    }
</script>

<style lang="scss">
    .multiselect {
        min-height: 34px;
    }

    .multiselect__select {
        min-height: 32px;
        height: 32px;
    }

    .multiselect__placeholder {
        margin-bottom: 4px;
        padding-top: 0;
        padding-left: 4px;
    }

    .multiselect__tags {
        padding-top: 6px;
        min-height: 34px;
        font-size: 14px;
        line-height: 1.42857143;
        color: #555555;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 4px;
        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        -webkit-transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
        -webkit-transition: border-color ease-in-out 0.15s, -webkit-box-shadow ease-in-out 0.15s;
        transition: border-color ease-in-out 0.15s, -webkit-box-shadow ease-in-out 0.15s;
        transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
        transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s, -webkit-box-shadow ease-in-out 0.15s;
    }

    .multiselect__tag {
        margin-bottom: 0;
        background: #26A69A;
    }

    .multiselect__tag-icon:focus, .multiselect__tag-icon:hover {
        background: rgba(0, 0, 0, 0.2);
    }

    .multiselect__input, .multiselect__single {
        margin-bottom: 6px;
        font-size: 14px;
        padding-left: 4px;

        &::placeholder {
            color: #adadad;
        }
    }

    .multiselect__option--highlight,
    .multiselect__option--highlight:after {
        background: #26A69A;
    }

    .multiselect__option:after {
        line-height: 34px;
    }

    .multiselect__option {
        font-size: 14px;
        padding: 8px 12px;
        min-height: 34px;
    }

    .multiselect__single {
        font-size: 14px;
    }

    .multiselect__option--group {
        font-weight: bold;
        color: #555555 !important;
    }

    .multiselect__option--selected.multiselect__option--highlight {
        background: #26A69A;
    }
</style>
