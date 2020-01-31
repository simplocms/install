<template>
    <div class="btn-group binary-switch" :class="sizeClass">
        <button type="button"
                class="btn"
                :class="[isOn ? onClass : 'btn-secondary']"
                @click.stop.prevent="turnOn"
        >{{ labelOn }}</button>
        <button type="button"
                class="btn"
                :class="[isOn ? 'btn-secondary' : onClass]"
                @click.stop.prevent="turnOff"
        >{{ labelOff}}</button>
    </div>
</template>

<script>
    export default {
        props: {
            name: {
                type: String,
                default: null
            },
            value: {
                type: null,
                default: false
            },
            labelOn: {
                type: String,
                default: 'On'
            },
            labelOff: {
                type: String,
                default: 'Off'
            },
            valueOn: {
                type: null,
                default: true
            },
            valueOff:  {
                type: null,
                default: false
            },
            onClass: {
                type: String,
                default: 'bg-teal-400'
            },
            size: String
        },

        computed: {
            isOn() {
                return this.valueOn === this.value;
            },

            sizeClass() {
                if (this.size) {
                    return 'btn-group-' + this.size;
                }

                return null;
            }
        },

        methods: {
            turnOn() {
                this.$emit('input', this.valueOn);
            },

            turnOff() {
                this.$emit('input', this.valueOff);
            }
        },
    }
</script>

<style lang="scss" scoped>
    .binary-switch {
        font-size: 14px;
        line-height: 1.42857143;
        color: #555555;
        border: 1px solid #ccc;
        border-radius: 4px;
        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        -webkit-transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
        -webkit-transition: border-color ease-in-out 0.15s, -webkit-box-shadow ease-in-out 0.15s;
        transition: border-color ease-in-out 0.15s, -webkit-box-shadow ease-in-out 0.15s;
        transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s;
        transition: border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s, -webkit-box-shadow ease-in-out 0.15s;

        .btn + .btn {
            margin-left: 0;
        }

        input {
            display: none;
        }
    }
</style>
