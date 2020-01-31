<template>
    <draggable-item handle=":scope > ._grid-toolbar > li > ._grid-move"
                    :type="item.type"
                    :item="item"
                    :source="sourceList"
                    :path="path"
    >
        <div class="_grid-column" :class="sizeClass">
            <content-controls @show-settings="showSettings"
                              @remove="confirmRemove"
                              @clone="clone"
                              :is-cloning="isCloning"
                              v-if="layoutEditMode"
                              :localization="localization"
            ></content-controls>

            <span class="_grid-column-resize"
                  @mousedown="startResize"
                  v-if="layoutEditMode"
                  v-html="isResizing ? activeLayoutSize : ''"
            ></span>

            <div class="_grid-bottom-controls" v-if="layoutEditMode">
                <button type="button" class="btn btn-grideditor" @click.prevent="addModule(false)">
                    <span class="plus">+</span> {{ localization.trans('column.btn_add_module') }}
                </button>
                <button type="button" class="btn btn-grideditor pull-right" @click.prevent="addRow()">
                    <span class="plus">+</span> {{ localization.trans('column.btn_add_row') }}
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
                    ></drop-zone>
                </template>
            </div>
        </div>
        <template slot="helper">
            <div class="column-helper">
                <i class="fa fa-columns"></i> {{ localization.trans('column.title') }}
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
                sortableItems: ['row', 'module'],
                resizingHelpers: null,
                activeLayout: window.GE.activeLayout,
                resizeStep: {
                    xs: 6
                },
                isResizing: false
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

        methods: {
            layoutChanged(layout) {
                this.activeLayout = layout;
            },

            showSettings() {
                this.$root.$emit('show::column-settings-modal', this.item, this.saveSettings);
            },

            saveSettings(settings) {
                for (const field in settings) {
                    this.item[field] = settings[field];
                }

                this.$root.$emit(EVENTS.CONTENT_CHANGED);
            },

            confirmRemove() {
                this.showRemoveConfirmation(
                    this.localization.trans('column.remove_title'),
                    this.localization.trans('column.remove_text')
                );
            },

            startResize(event) {
                this.resizingHelpers = {
                    x: event.clientX,
                    moveBy: $(this.$el).closest('.row').first().innerWidth() / 12
                };
                this.isResizing = true;

                const $document = $('body');

                $document.addClass('_grid_col_resizing');
                $document.on('mousemove', this.resizing);
                $document.on('mouseup', this.stopResize);
                $(window).on('mouseout', event => {
                    const mouseX = event.clientX;
                    const mouseY = event.clientY;

                    if (mouseX < 0 || mouseY < 0 || mouseX > window.innerWidth || mouseY > window.innerHeight) {
                        this.stopResize();
                    }
                });
            },

            /**
             * Stop resizing column.
             */
            stopResize() {
                this.isResizing = false;
                const $document = $('body');

                $document.off('mousemove');
                $document.off('mouseup');
                $(window).off('mouseout');
                $document.removeClass('_grid_col_resizing');
            },

            /**
             * Resize column.
             * @param {MouseEvent} event
             */
            resizing(event) {
                const step = this.resizeStep[this.activeLayout] || 1;
                const diff = this.resizingHelpers.x - event.clientX;
                const breakpoints = this.getBreakPoints(step);
                const lowerBreakPointDistance = this.resizingHelpers.moveBy * (this.activeLayoutSize - breakpoints.lower);
                const higherBreakPointDistance = this.resizingHelpers.moveBy * (breakpoints.higher - this.activeLayoutSize) * -1;

                if (diff > lowerBreakPointDistance && this.activeLayoutSize > step) {
                    this.activeLayoutSize -= step;
                    this.resizingHelpers.x -= lowerBreakPointDistance;
                } else if (diff < higherBreakPointDistance && this.activeLayoutSize < 12) {
                    this.activeLayoutSize += step;
                    this.resizingHelpers.x += higherBreakPointDistance * -1;
                }
            },

            /**
             * Get closest lower and higher breakpoint for specified step and position.
             * @param {number} pos
             * @param {number} step
             * @return {{lower: number, higher: number}}
             */
            getBreakPoints(step) {
                const pos = this.activeLayoutSize;
                const result = {
                    lower: (pos - (pos % step)) || step
                };

                if (result.lower < step) {
                    result.lower = step;
                }

                result.higher = result.lower + step;

                if (result.lower === pos) {
                    if (result.lower - step >= step) {
                        result.lower -= step;
                    }
                    result.higher = pos + step;
                }

                if (result.higher > 12) {
                    result.higher = 12;
                }

                return result;
            },
        },

        computed: {
            sizeClass() {
                return `col-xs-${this.activeLayoutSize}`;
            },

            activeLayoutSize: {
                get() {
                    let cols = 12;

                    if (!this.item.size) {
                        return cols;
                    }

                    if (!this.activeLayout) {
                        return this.item.size.col || cols;
                    }
                    return this.item.size[this.activeLayout] || cols;
                },
                set(cols) {
                    this.item.size[this.activeLayout || 'col'] = cols;
                    this.item.size = {...this.item.size};
                }
            }
        }
    }
</script>
