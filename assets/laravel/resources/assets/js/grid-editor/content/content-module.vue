<template>
    <draggable-item handle="._grid-move" type="module" :item="item" :source="sourceList" :path="path">
        <div class="_grid-module">
            <div class="_grid-module-header">
            <span class="_grid-module-move" :class="{'_grid-move': layoutEditMode}">
                <span v-if="!preview">
                    <i class="fa fa-spinner fa-spin fa-fw"></i> {{ localization.trans('module.loader') }}
                </span>
                <span v-else>
                    <i class="fa" :class="'fa-' + item.icon"></i> {{ item.title }}
                </span>
            </span>
                <ul class="_grid-toolbar" v-if="layoutEditMode">
                    <li>
                        <button class="btn btn-grideditor"
                                :title="localization.trans('module.btn_remove')"
                                type="button"
                                @click.prevent="confirmRemove"
                        >
                            <i class="fa fa-trash-o"></i>
                        </button>
                    </li>
                    <li>
                        <button class="btn btn-grideditor"
                                :title="localization.trans('module.btn_duplicate')"
                                type="button"
                                @click.prevent="clone"
                        >
                            <i class="fa fa-spinner fa-spin" v-if="isCloning"></i>
                            <i class="fa fa-copy" v-else></i>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="_grid-module-preview" @mouseover="mouseOver" @mouseleave="mouseLeave">
                <div v-if="!preview">
                    ...
                </div>
                <div v-html="preview"></div>
                <div class="_grid-module-overlap" v-show="showEdit">
                    <button class="btn bg-teal-400" type="button" @click.prevent="showSettings">
                        <i class="fa fa-pencil"></i> {{ localization.trans('module.btn_edit') }}
                    </button>
                </div>
            </div>
        </div>

        <template slot="helper">
            <div class="module-helper">
                <span v-if="!preview">
                    <i class="fa fa-spinner fa-spin fa-fw"></i> {{ localization.trans('module.loader') }}
                </span>
                <span v-else>
                    <i class="fa" :class="'fa-' + item.icon"></i> {{ item.title }}
                </span>
            </div>
        </template>
    </draggable-item>
</template>

<script>
    import ContentBehaviourMixin from './content-behaviour-mixin';
    import {EVENTS} from '../enums';

    const options = window.gridEditorOptions();

    export default {
        mixins: [ContentBehaviourMixin],

        data() {
            return {
                preview: this.item.preview,
                showEdit: false
            }
        },

        created() {
            if (this.item.entity_id) {
                const event = this.item.universal ? EVENTS.UNIVERSAL_PREVIEW_LOADED : EVENTS.PREVIEW_LOADED;
                this.$root.$on(event + this.item.entity_id, this.previewLoaded);
            }
        },

        destroyed() {
            if (this.item.entity_id) {
                const event = this.item.universal ? EVENTS.UNIVERSAL_PREVIEW_LOADED : EVENTS.PREVIEW_LOADED;
                this.$root.$off(event + this.item.entity_id, this.previewLoaded);
            }
        },

        methods: {
            cloneItemAndContent() {
                return new Promise((resolve, reject) => {
                    const output = {
                        type: this.item.type,
                        preview: this.item.preview,
                        title: this.item.title,
                        name: this.item.name,
                        icon: this.item.icon,
                    };

                    if (this.item.url) {
                        output.url = this.item.url;
                    } else {
                        output.url = options.modules.find(module => module.name === output.name).url;
                    }

                    if (this.item.configuration) {
                        output.configuration = JSON.parse(JSON.stringify(this.item.configuration));
                        resolve(output);
                    } else if (this.item.entity_id) {
                        this.fetchEntityConfiguration()
                            .then(response => {
                                output.configuration = response.configuration;
                                resolve(output);
                            })
                            .catch(reject)
                    }
                });
            },

            fetchEntityConfiguration() {
                return new Promise((resolve, reject) => {
                    Request.get(options.urls.entityConfiguration + '/' + this.item.entity_id)
                        .done(response => {
                            resolve(response);
                        })
                        .fail(() => reject());
                });
            },

            mouseOver() {
                this.showEdit = this.preview !== null;
            },

            mouseLeave() {
                this.showEdit = false;
            },

            previewLoaded(module) {
                this.item.name = module.name;
                this.item.title = module.title;
                this.item.icon = module.icon;
                this.preview = this.item.preview = module.content;
            },

            getOutputItem() {
                const output = {
                    type: this.item.type
                };

                if (this.item.entity_id) {
                    output.entity_id = this.item.entity_id;
                } else {
                    output.name = this.item.name;
                }

                if (this.item.configuration) {
                    output.configuration = this.item.configuration;
                }

                if (this.item.universal) {
                    output.universal = true;
                }

                return output;
            },

            showSettings() {
                this.$root.$emit('show::module-form-modal', this.item, module => {
                    this.preview = this.item.preview = module.preview;
                    this.item.configuration = module.configuration;

                    this.$root.$emit(EVENTS.CONTENT_CHANGED);
                });
            },

            confirmRemove() {
                this.showRemoveConfirmation(
                    this.localization.trans('module.remove_title'),
                    this.localization.trans('module.remove_text')
                );
            },
        },
    }
</script>
