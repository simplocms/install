<template>
    <draggable-item handle=":scope > ._grid-toolbar > li > ._grid-move"
                    :type="item.type"
                    :item="item"
                    :source="sourceList"
                    :path="path"
    >
        <div class="row">
            <content-controls @show-settings="showSettings"
                              @remove="confirmRemove"
                              @clone="clone"
                              :is-cloning="isCloning"
                              v-if="layoutEditMode"
                              :localization="localization"
            ></content-controls>

            <div class="_grid-bottom-controls" v-if="layoutEditMode">
                <button type="button" class="btn btn-grideditor" @click.prevent="addColumn(false)">
                    <span class="plus">+</span> {{ localization.trans('row.btn_add_column') }}
                </button>
            </div>

            <div class="_grid-content" ref="content" :class="[receiveClass]">
                <drop-zone :accept="sortableItems"
                           :target="innerContent"
                           :path="path"
                ></drop-zone>

                <template v-if="innerContent"
                          v-for="(contentObject, index) in innerContent">
                    <div :key="contentObject.uuid"
                         :path="path + '-' + index"
                         :is="'cms-ge-content-' + contentObject.type"
                         :item="contentObject"
                         ref="contentItem"
                         :layout-edit-mode="layoutEditMode"
                         @remove="removeContentItem(index)"
                         @cloned="addContent"
                         :localization="localization"
                         :source-list="innerContent"
                    ></div>

                    <drop-zone :accept="sortableItems"
                               :target="innerContent"
                               :position="index + 1"
                               :path="path"
                               :class="'size-adjusting col-xs-' + getDropZoneSize(index)"
                    ></drop-zone>
                </template>
            </div>
        </div>
        <template slot="helper">
            <div class="row-helper">
                <i class="fa fa-window-minimize"></i> {{ localization.trans('row.title') }}
            </div>
        </template>
    </draggable-item>
</template>

<script>
    import ContentControls from './content-controls';
    import ContentBehaviourMixin from './content-behaviour-mixin';
    import {EVENTS} from '../enums';

    export default {
        mixins: [ContentBehaviourMixin],

        data() {
            return {
                sortableItems: ['column'],
                activeLayout: window.GE.activeLayout,
            }
        },

        components: {
            'content-controls': ContentControls
        },

        created() {
            this.$root.$on(EVENTS.LAYOUT_CHANGED, this.layoutChanged);
        },

        destroyed() {
            this.$root.$off(EVENTS.LAYOUT_CHANGED, this.layoutChanged);
        },

        computed: {
            dropZoneSizes() {
                const sizes = {};
                let rowSum = 0;
                for (const i in this.innerContent) {
                    const columnSize = this.getColumnActiveLayoutSize(this.innerContent[i]);
                    rowSum += columnSize;

                    if (rowSum === 12) {
                        sizes[i] = 12;
                        rowSum = 0;
                        if (i > 0 && columnSize !== 12) {
                            sizes[i - 1] = 0;
                        }
                    } else if (rowSum > 12) {
                        sizes[i] = 12 - columnSize;
                        rowSum = sizes[i];
                    } else {
                        sizes[i] = 12 - rowSum;
                        if (i > 0 && rowSum !== columnSize) {
                            sizes[i - 1] = 0;
                        }
                    }

                    if (columnSize === 12) {
                        sizes[i] = 12;
                    }
                }

                return sizes;
            }
        },

        methods: {
            layoutChanged(layout) {
                this.activeLayout = layout;
            },

            showSettings() {
                this.$root.$emit('show::row-settings-modal', this.item, this.saveSettings);
            },

            getColumnActiveLayoutSize(column) {
                let cols = 12;

                if (!column.size) {
                    return cols;
                }

                if (!this.activeLayout) {
                    return column.size.col || cols;
                }
                return column.size[this.activeLayout] || cols;
            },

            getDropZoneSize(index) {
                return this.dropZoneSizes[index];
            },

            saveSettings(settings) {
                for (const field in settings) {
                    this.item[field] = settings[field];
                }

                this.$root.$emit(EVENTS.CONTENT_CHANGED);
            },

            confirmRemove() {
                this.showRemoveConfirmation(
                    this.localization.trans('row.remove_title'),
                    this.localization.trans('row.remove_text')
                );
            },
        }
    }
</script>
