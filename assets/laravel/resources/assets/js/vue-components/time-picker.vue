<template>
    <input :placeholder="placeholder" :name="name" type="time">
</template>

<script>
    export default {
        data() {
            return {
                picker: null,
            };
        },

        props: {
            value: {
                type: String,
                default: null
            },
            name: {
                type: String,
                default: null
            },
            placeholder: {
                type: String,
                default: null
            }
        },

        mounted() {
            this.picker = $(this.$el).pickatime({
                format: "H:i",
                onSet: this.onSet
            });

            this.picker.val(this.value);
        },

        methods: {
            onSet() {
                this.$emit('input', this.picker.val() || null);
            }
        },

        watch: {
            value(newValue) {
                this.picker.val(newValue);
            }
        },

        beforeDestroy() {
            if (this.picker) {
                this.picker.stop();
            }
        }
    }
</script>
