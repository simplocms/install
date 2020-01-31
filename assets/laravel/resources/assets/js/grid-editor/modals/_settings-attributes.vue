<template>

    <fieldset class="content-group">
        <legend class="text-bold">
            <i class="fa fa-cog"></i> {{ localization.trans('settings_modal.attributes.legend') }}
        </legend>

        <table class="table">
            <tr v-for="(attribute, index) in innerAttributes">
                <td>
                    <!-- Attribute Name -->
                    <div class="form-group mt-5 mb-5">
                        <label :for="'input-' + id + '-attr-name-' + index">
                            {{ localization.trans('settings_modal.attributes.label_name') }}
                        </label>
                        <input :id="'input-' + id + '-attr-name-' + index"
                               type="text"
                               class="form-control"
                               v-model="attribute.name"
                        >
                    </div>
                </td>
                <td>
                    <!-- Attribute Value -->
                    <div class="form-group col-xs-12 mt-5  mb-5">
                        <label :for="'input-' + id + '-attr-val-' + index">
                            {{ localization.trans('settings_modal.attributes.label_value') }}
                        </label>
                        <input :id="'input-' + id + '-attr-val-' + index"
                               type="text"
                               class="form-control"
                               v-model="attribute.value"
                        >
                    </div>
                </td>
                <td class="pt-20">
                    <button type="button"
                            @click.prevent="removeAttribute(index)"
                            class="close"
                    >
                        <span>×</span>
                    </button>
                </td>
            </tr>
        </table>
        <a href="#" class="text-muted" @click.prevent="addAttribute">
            <i class="fa fa-plus"></i> {{ localization.trans('settings_modal.attributes.btn_add') }}
        </a>
    </fieldset>
</template>

<script>
    const EMPTY_ATTRIBUTE = {
        name: '',
        value: ''
    };

    export default {
        data() {
            return {
                innerAttributes: this.attributes,
                id: (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1)
            }
        },

        props: {
            attributes: Array,
            localization: Object
        },

        methods: {
            addAttribute() {
                this.innerAttributes.push({...EMPTY_ATTRIBUTE});
            },

            removeAttribute(index) {
                this.innerAttributes.splice(index, 1);
            },

            getAttributes() {
                // Fitler out empty attributes
                return this.innerAttributes.filter(attribute => {
                    return attribute.name !== '';
                })
            }
        },

        watch: {
            attributes: {
                handler(val) {
                    if (val && val.length) {
                        this.innerAttributes = [...val];
                    } else {
                        this.innerAttributes = [{...EMPTY_ATTRIBUTE}];
                    }
                },
                immediate: true
            }
        }
    }
</script>
