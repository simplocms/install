<template>
    <div :class="groupClass">
        <slot></slot>
        <span class="help-block" v-show="error !== null">{{ error }}</span>
    </div>
</template>

<script>
    export default {
        props: {
            required: {
                type: Boolean,
                default: false
            },
            error: {
                type: String,
                default: null
            },
            hasError: {
                type: Boolean,
                default: false
            }
        },

        computed: {
            groupClass() {
                const classes = ['form-group'];

                if (this.required) {
                    classes.push('required');
                }

                if (this.error !== null || this.hasError) {
                    classes.push('has-error');
                }

                return classes;
            }
        }
    }
</script>
