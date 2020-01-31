<template>
    <div class="popover-wrapper">
        <slot name="toggle"
              :openHandler="openPopup"
        ></slot>
        <div class="popover editable-container editable-popup fade top in"
             style="display: block"
             ref="popover"
             v-if="isOpened"
        >
            <div class="arrow" style="left: 50.1488%;"></div>
            <h3 class="popover-title" v-if="title">{{ title }}</h3>
            <div class="popover-content form-inline">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script>
    import Popper from 'popper.js';

    export default {
        data() {
            return {
                isOpened: false,
                popper: null
            };
        },

        props: {
            title: {
                type: String,
                default: null
            },
        },

        methods: {
            openPopup() {
                if (this.isOpened) {
                    return;
                }

                this.isOpened = true;
                this.$nextTick(this.initializePopper);
            },

            initializePopper() {
                this.popper = new Popper(this.$el.firstChild, this.$refs.popover, {
                    placement: 'top',
                    modifiers: {offset: {offset: '0, 10px'}}
                });

                this.initializeEvents();
            },

            initializeEvents() {
                document.addEventListener('click', this.onClickDocument);
            },

            deinitializeEvents() {
                document.removeEventListener('click', this.onClickDocument);
            },

            onClickDocument(event) {
                if (!this.isChildOfPopover(event.target)) {
                    this.close();
                }
            },

            close() {
                this.deinitializeEvents();
                this.isOpened = false;
                this.$nextTick(() => {
                    this.popper.destroy();
                });
            },

            isChildOfPopover(el) {
                let current = el;

                while (current.tagName.toLowerCase() !== 'body') {
                    if (current.classList.contains('popover')) {
                        return true;
                    }

                    current = current.parentNode;
                }

                return false;
            }
        },
    }
</script>

<style lang="scss" scoped>
    .popover-wrapper {
        display: inline;
    }

    .popover-title {
        padding: 10px 15px 5px 15px;
        font-weight: 700;
    }

    .popover[x-placement="bottom"] > .arrow {
        transform: rotate(180deg);
        top: -11px;

        &:after {
            border-top-color: #f7f7f7;
        }
    }
</style>
