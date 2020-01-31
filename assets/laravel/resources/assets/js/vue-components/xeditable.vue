<template>
    <div class="xeditable-wrapper">
        <a href="#"
           ref="link"
           @click.prevent="openPopup"
           class="editable editable-click"
           :class="{'editable-empty': isEmpty, 'editable-open': isOpened}"
        >
            {{ innerText }}
        </a>
        <div class="popover editable-container editable-popup fade top in"
             style="display: block"
             ref="popover"
             v-if="isOpened"
        >
            <div class="arrow" style="left: 50.1488%;"></div>
            <h3 class="popover-title" v-if="title">{{ title }}</h3>
            <div class="popover-content form-inline">
                <div class="control-group form-group">
                    <div>
                        <div class="editable-input" style="position: relative;">
                            <input type="text"
                                   class="form-control"
                                   style="padding-right: 24px;"
                                   v-model="innerValue"
                                   ref="inputField"
                                   @keydown.enter="submit"
                            >
                        </div>
                        <div class="editable-buttons">
                            <button type="button"
                                    @click.prevent="submit"
                                    class="btn btn-primary btn-sm editable-submit"
                            >
                                <i class="fa fa-fw fa-check"></i>
                            </button>
                            <button type="button"
                                    @click.prevent="cancel"
                                    class="btn btn-default btn-sm editable-cancel"
                            >
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="editable-error-block help-block" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Popper from 'popper.js';

    export default {
        data() {
            return {
                innerPhotos: [],
                isOpened: false,
                linkOffset: {left: 0, top: 0},
                popper: null,
                innerValue: this.value
            };
        },

        props: {
            value: String,
            title: {
                type: String,
                default: null
            },
            fallback: {
                type: String,
                default: '--'
            },
        },

        methods: {
            submit() {
                this.$emit('input', this.innerValue);
                this.close();
            },

            cancel() {
                this.innerValue = this.value;
                this.close();
            },

            openPopup() {
                if (this.isOpened) {
                    return;
                }

                this.isOpened = true;
                this.$nextTick(this.initializePopper);
            },

            initializePopper() {
                this.popper = new Popper(this.$refs.link.parentNode, this.$refs.popover, {
                    placement: 'top',
                    modifiers: {offset: {offset: '0, 10px'}}
                });

                this.$refs.inputField.focus();
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

        computed: {
            isEmpty() {
                return this.value === null || this.value.length === 0;
            },

            innerText() {
                return this.isEmpty ? this.fallback : this.value;
            }
        },

        watch: {
            value(newValue) {
                this.innerValue = newValue;
            }
        }
    }
</script>

<style scoped>
    .xeditable-wrapper {
        display: inline;
    }

    .popover-title {
        padding: 10px 15px 5px 15px;
        font-weight: 700;
    }
</style>
