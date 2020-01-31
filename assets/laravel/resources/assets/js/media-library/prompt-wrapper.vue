<template>
    <div tabindex="-1"
         role="dialog"
         :id="id"
         class="modal fade"
         data-parent="#app"
         ref="modal"
    >
        <div role="document" class="modal-dialog modal-lg">
            <div class="modal-content">
                <media-library v-if="isOpen"
                               :file-type="fileType"
                               :multi-select="isMultiSelect"
                               ref="mediaLibrary"
                               :is-prompt="true"
                               @close="close"
                               :trans="trans"
                               :warn-cache-driver="warnCacheDriver"
                               :sort-by="sortByDefault"
                               :sort-dir="sortDirDefault"
                               @sort="changeDefaultSorting"
                ></media-library>
            </div>
        </div>
    </div>
</template>

<script>
    import {COMMANDS, EVENTS} from "./enums";

    export default {
        data() {
            return {
                id: 'media-library-prompt-modal',
                params: null,
                fileType: null,
                isMultiSelect: false,
                isOpen: false,
                $modal: null,
                sortByDefault: this.sortBy,
                sortDirDefault: this.sortDir,
            }
        },

        props: {
            trans: {
                type: Object,
                required: true
            },

            warnCacheDriver: {
                type: Boolean,
                default: false
            },

            sortBy: {
                type: String,
                default: null
            },

            sortDir: {
                type: String,
                default: null
            }
        },

        methods: {
            hide() {
                this.$modal.modal('hide');
            },

            open(params) {
                this.params = params;
                this.fileType = params.type;
                this.isMultiSelect = params.multi;
                this.isOpen = true;

                this.$modal.modal({
                    backdrop: 'static'
                });

                this.$nextTick(() => {
                    this.initialize();
                });
            },

            initialize() {
                if (this.isMultiSelect && this.params.files) {
                    this.$refs.mediaLibrary.selectFiles(this.params.files);
                } else if (!this.isMultiSelect) {
                    if (this.params.file) {
                        this.$store.dispatch('MediaLibrary/activateFile', this.params.file);
                    }
                    EventBus.$on(EVENTS.FILE_SELECTION_CONFIRMED, this.selectionConfirmed);
                }

                if (this.params.onSelect) {
                    EventBus.$on(EVENTS.FILE_SELECTED, this.params.onSelect);
                }

                if (this.params.onUnselect) {
                    EventBus.$on(EVENTS.FILE_UNSELECTED, this.params.onUnselect);
                }
            },

            selectionConfirmed(selection) {
                this.params.ok(selection);
                this.hide();
            },

            close() {
                this.hide();
            },

            onHidden() {
                EventBus.$off(EVENTS.FILE_SELECTION_CONFIRMED, this.selectionConfirmed);
                EventBus.$off(EVENTS.FILE_SELECTED, this.params.onSelect);
                EventBus.$off(EVENTS.FILE_UNSELECTED, this.params.onUnselect);
                this.isOpen = false;
            },

            changeDefaultSorting(option) {
                this.sortByDefault = option.by;
                this.sortDirDefault = option.direction;
            }
        },

        mounted() {
            this.$modal = $(this.$refs.modal);
            this.$modal.on('hidden.bs.modal', this.onHidden);

            EventBus.$on(COMMANDS.OPEN_PROMPT, this.open);
        },

        destroyed() {
            EventBus.$off(COMMANDS.OPEN_PROMPT, this.open);
        }
    }
</script>

<style scoped>
    @media (min-width: 1200px) {
        .modal-lg {
            width: 1100px;
        }
    }

    .modal-content {
        max-height: 90vh;
        overflow: auto;
    }
</style>
