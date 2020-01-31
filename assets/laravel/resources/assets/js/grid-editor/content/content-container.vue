<template>
    <draggable-item handle=":scope > ._grid-toolbar > li > ._grid-move"
                    :type="item.type"
                    :item="item"
                    :source="sourceList"
                    :path="path"
    >
        <div class="_grid-container">
            <content-controls @show-settings="showSettings"
                              @remove="confirmRemove"
                              @clone="clone"
                              :is-cloning="isCloning"
                              v-if="layoutEditMode"
                              :localization="localization"
            ></content-controls>

            <div class="_grid-bottom-controls" v-if="layoutEditMode">
                <button type="button" class="btn btn-grideditor" @click.prevent="addModule(false)">
                    <span class="plus">+</span> {{ localization.trans('container.btn_add_module') }}
                </button>
                <button type="button" class="btn btn-grideditor pull-right" @click.prevent="addRow()">
                    <span class="plus">+</span> {{ localization.trans('container.btn_add_row') }}
                </button>
            </div>

            <div class="_grid-content" :class="[receiveClass]" ref="content">
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
            <div class="container-helper">
                <i class="fa fa-object-group"></i> {{ localization.trans('container.title') }}
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
            }
        },

        components: {
            'content-controls': ContentControls
        },

        methods: {
            showSettings() {
                this.$root.$emit('show::container-settings-modal', this.item, this.saveSettings);
            },

            saveSettings(settings) {
                for (const field in settings) {
                    this.item[field] = settings[field];
                }

                this.$root.$emit(EVENTS.CONTENT_CHANGED);
            },

            confirmRemove() {
                this.showRemoveConfirmation(
                    this.localization.trans('container.remove_title'),
                    this.localization.trans('container.remove_text')
                );
            },
        }
    }
</script>
