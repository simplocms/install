<template>
    <form :method="method"
          :action="action"
          accept-charset="UTF-8"
          @submit="submit"
    >
        <slot></slot>
        <spinner-lock :is-locked="isLocked"></spinner-lock>
    </form>
</template>

<script>
    import SpinnerLock from './spinner-lock';

    export default {
        props: {
            /** @type {string} */
            action: {
                type: String,
                default: null
            },
            /** @type {string} */
            method: {
                type: String,
                default: 'post',
                validator: function (value) {
                    return ['post', 'put', 'patch'].indexOf(value.toLowerCase()) !== -1;
                }
            },
            /** @type {Form} */
            form: {
                type: Object,
                default: null
            }
        },

        components: {
            'spinner-lock': SpinnerLock
        },

        computed: {
            isLocked() {
                return this.form ? this.form.isLocked() : false;
            }
        },

        methods: {
            submit($event) {
                if (!this.form) {
                    return;
                }

                $event.preventDefault();

                if (!this.form.lock()) {
                    return;
                }

                this.form[this.method.toLowerCase()](this.action)
                    .then(response => {
                        this.form.resetChangeState();
                        this.$emit('success', response);
                    })
                    .catch(thrown => {
                        this.$emit('error', thrown);
                        $.jGrowl(this.$root.localization.trans('notifications.validation_failed'), {
                            header: this.$root.localization.trans('flash_level.danger'),
                            theme: 'bg-danger'
                        });
                    });
            }
        }
    }
</script>
