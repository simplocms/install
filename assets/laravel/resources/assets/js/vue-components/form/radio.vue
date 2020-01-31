<template>
    <label class="radio-wrapper">
        <slot></slot>
        <input type="radio"
               :name="name"
               :value="inputValue"
               v-model="mutableValue"
        >
        <span class="checkmark"></span>
    </label>
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
            inputValue: [Number, String],
            value: [Number, String]
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
    /* Customize the label */
    .radio-wrapper {
        display: block;
        position: relative;
        padding-left: 28px;
        margin-top: 8px;
        margin-bottom: 8px;
        cursor: pointer;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    /* Hide the browser's default radio button */
    .radio-wrapper input {
        position: absolute;
        opacity: 0;
    }

    /* Create a custom radio button */
    .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 18px;
        width: 18px;
        border: 2px solid #607d8b;
        border-radius: 50%;
    }

    /* On mouse-over, add a grey background color */
    .radio-wrapper:active input ~ .checkmark:after {
        opacity: 0.75;
        display: block;
    }

    /* On mouse-over, add a grey background color */
    .radio-wrapper input:not(:checked):focus ~ .checkmark:after {
        opacity: 0.25;
        display: block;
    }

    /* Create the indicator (the dot/circle - hidden when not checked) */
    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    /* Show the indicator (dot/circle) when checked */
    .radio-wrapper input:checked ~ .checkmark:after {
        display: block;
    }

    /* Style the indicator (dot/circle) */
    .radio-wrapper .checkmark:after {
        top: 3px;
        left: 3px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #607d8b;
    }
</style>
