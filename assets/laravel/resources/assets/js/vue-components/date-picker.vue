<template>
    <input :placeholder="placeholder" :name="name" type="date">
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
            this.picker = $(this.$el).pickadate({
                monthsFull: ['Leden', 'Únor', 'Březen', 'Duben', 'Květen', 'Červen', 'Červenec', 'Srpen', 'Září', 'Říjen', 'Listopad', 'Prosinec'],
                weekdaysShort: ['Ne', 'Po', 'Út', 'St', 'Čt', 'Pá', 'So'],
                today: 'Dnes',
                clear: 'Smazat',
                close: 'Zavřít',
                format: 'dd.mm.yyyy',
                formatSubmit: 'yyyy-mm-dd',
                hiddenSuffix: '_formatted',
                firstDay: 1,
                onSet: this.onSet,
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
