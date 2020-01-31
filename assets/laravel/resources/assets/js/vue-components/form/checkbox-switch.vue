<template>
    <div class="checkbox-switch" :class="{checked: mutableValue}">
        <label @click.stop.prevent="toggle">
            <input :name="name"
                   type="checkbox"
                   v-model="mutableValue"
            >
            <span><small></small></span>
            <slot></slot>
        </label>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                mutableValue: this.value
            };
        },

        props: {
            name: {
                type: String,
                default: null
            },
            value: {
                type: Boolean,
                default: false
            }
        },

        methods: {
            toggle() {
                this.mutableValue = !this.mutableValue;
            }
        },

        watch: {
            mutableValue(newVal) {
                this.$emit('input', newVal);
            },

            value(newVal) {
                this.mutableValue = newVal;
            }
        }
    }
</script>

<style lang="scss" scoped>
    .checkbox-switch {
        margin-bottom: 14px;
        padding-left: 0;

        > label {
            position: relative;
            padding-left: 56px;
            margin: 0;
            cursor: pointer;

            > input {
                display: none;
            }

            > span {
                border: 1px solid rgb(223, 223, 223);
                border-radius: 100px;
                cursor: pointer;
                display: inline-block;
                width: 44px;
                height: 22px;
                vertical-align: middle;
                box-sizing: content-box;
                position: absolute;
                left: 0;
                margin-top: -2px;
                background-color: rgb(255, 255, 255);
                box-shadow: rgb(223, 223, 223) 0 0 0 0 inset;
                transition: border 0.4s ease 0s, box-shadow 0.4s ease 0s;

                > small {
                    background-color: #fff;
                    border-radius: 100px;
                    width: 22px;
                    height: 22px;
                    position: absolute;
                    top: 0;
                    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
                    left: 0;
                    transition: background-color 0.4s ease 0s, left 0.2s ease 0s;
                }
            }
        }

        &.checked {
            > label > span {
                background-color: rgb(100, 189, 99);
                border-color: rgb(100, 189, 99);
                box-shadow: rgb(100, 189, 99) 0 0 0 12px inset;
                transition: border 0.4s ease 0s, box-shadow 0.4s ease 0s, background-color 1.2s ease 0s;

                > small {
                    left: 22px;
                    transition: background-color 0.4s ease 0s, left 0.2s ease 0s;
                    background-color: rgb(255, 255, 255);
                }
            }
        }
    }
</style>
