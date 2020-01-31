<template>
    <transition name="slide" v-on:after-enter="onFullyVisible">
        <div class="file-detail" v-show="isOpen">
            <div v-if="file">
                <div class="close-button" @click.prevent="close">
                    <i class="fa fa-arrow-right"></i>
                </div>

                <div class="filename">
                    <div class="form-group" v-if="isRenaming">
                        <div class="input-group">
                            <input type="text" :value="file.getName()" class="form-control" ref="nameInput"/>
                            <div class="input-group-btn">
                                <button class="btn bg-teal-400" @click.prevent="rename">
                                    <i class="fa" :class="[locks.name ? 'fa-spinner fa-spin' : 'fa-check']"></i>
                                </button>
                                <button class="btn btn-default" @click.prevent="cancelRenaming">
                                    <i class="fa fa-close"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div v-else @click.prevent="startRenaming">
                        <div class="info">
                            {{ file.getNameWithExtension() }}
                        </div>
                        <i class="fa fa-pencil"></i>
                    </div>
                </div>

                <div class="image-thumbnail" v-if="file.hasPreview()">
                    <img :src="file.getPreview().fitToCanvas(390, 220, 'F5F5F5').preview().getUrl()"
                         width="390"
                         height="220"
                         draggable="false"
                    >
                </div>

                <div class="row info-container">
                    <div class="col-xs-3">
                        <span class="info-header">{{ localization.trans('file.size') }}</span>
                        {{ file.getHumanSize() }}
                    </div>
                    <div class="col-xs-4" v-if="file.isImage() && file.isResolutionAvailable()">
                        <span class="info-header">{{ localization.trans('file.resolution') }}</span>
                        {{ file.getResolutionText() }}
                    </div>
                    <div class="col-xs-5">
                        <span class="info-header">{{ localization.trans('file.last_change_at') }}</span>
                        {{ file.getCreatedAt() }}
                    </div>
                </div>

                <div class="form-group">
                    <label>{{ localization.trans('file.url') }}</label>
                    <a href="#" @click.prevent="copyUrl" class="pull-right">
                        {{ localization.trans('file.copy_url') }}
                    </a>
                    <input type="text" readonly :value="file.getUrl()" class="form-control" ref="urlField"/>
                </div>

                <div class="form-group">
                    <label>{{ localization.trans('file.description') }}</label>

                    <div class="input-group">
                        <input type="text" :value="file.getDescription()" class="form-control" ref="descriptionInput"/>
                        <div class="input-group-btn">
                            <button class="btn bg-teal-400" @click.prevent="updateDescription">
                                <i class="fa fa-fw fa-spinner fa-spin" v-show="locks.description"></i>
                                {{ localization.trans('file.btn_save_description') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="actions-heading">{{ localization.trans('file.actions') }}</div>

                <div class="row">
                    <div class="col-xs-6">
                        <div class="action-button">
                            <a href="#" @click.prevent="startRenaming">
                                <i class="fa fa-fw"
                                   :class="[locks.name ? 'fa-spinner fa-spin' : 'fa-pencil']"
                                ></i> {{ localization.trans('file.btn_rename') }}
                            </a>
                        </div>
                        <div class="action-button">
                            <a href="#" @click.prevent="selectFileReplacement()">
                                <i class="fa fa-fw"
                                   :class="[locks.override ? 'fa-spinner fa-spin' : 'fa-upload']"
                                ></i> {{ localization.trans('file.btn_override') }}
                            </a>
                        </div>
                        <div class="action-button">
                            <a href="#" @click.prevent="remove">
                                <i class="fa fa-fw"
                                   :class="[locks.remove ? 'fa-spinner fa-spin' : 'fa-trash-o']"
                                ></i> {{ localization.trans('file.btn_delete') }}
                            </a>
                        </div>
                    </div>
                    <div class="col-xs-6" v-show="file.isImage() && file.isResolutionAvailable()">
                        <div class="action-button">
                            <a href="#" @click.prevent="resizeImage">
                                <i class="fa fa-fw"
                                   :class="[locks.resize ? 'fa-spinner fa-spin' : 'fa-arrows-alt']"
                                ></i> {{ localization.trans('file.btn_change_resolution') }}
                            </a>
                        </div>
                        <div class="action-button">
                            <a href="#" @click.prevent="rotateLeft">
                                <i class="fa fa-fw"
                                   :class="[locks.rotateLeft ? 'fa-spinner fa-spin' : 'fa-rotate-left']"
                                ></i> {{ localization.trans('file.btn_rotate_left_90') }}
                            </a>
                        </div>
                        <div class="action-button">
                            <a href="#" @click.prevent="rotateRight">
                                <i class="fa fa-fw"
                                   :class="[locks.rotateRight ? 'fa-spinner fa-spin' : 'fa-rotate-right']"
                                ></i> {{ localization.trans('file.btn_rotate_right_90') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <input type="file"
                   @change="fileReplacementSelected"
                   ref="fileInput"
                   style="display: none"
            >

            <image-resize-modal ref="imageReResizeModal" :localization="localization"></image-resize-modal>
        </div>
    </transition>
</template>

<script>
    import {EVENTS, COMMANDS, DETAIL_INVOCABLE_ACTIONS} from "../enums";
    import MediaService from "../service";
    import Uploader from "../uploader";
    import ImageResizeModal from './image-resize-modal';

    export default {
        data() {
            return {
                file: null,
                isRenaming: false,
                isVisible: false,
                action: null,
                renameAfterAnimation: false,
                locks: {
                    name: false,
                    description: false,
                    rotateLeft: false,
                    rotateRight: false,
                    remove: false,
                    override: false,
                    resize: false
                }
            };
        },

        props: {
            localization: Object
        },

        components: {
            'image-resize-modal': ImageResizeModal
        },

        computed: {
            isOpen() {
                return this.file !== null;
            },

            isPrompt() {
                return this.$store.state.MediaLibrary.isPrompt;
            }
        },

        methods: {
            show(file, action = DETAIL_INVOCABLE_ACTIONS.NONE) {
                this.file = file;
                this.isRenaming = false;
                this.invokeAction(action);

                if (!this.isPrompt) {
                    this.$store.dispatch('MediaLibrary/activateFile', this.file);
                }
            },

            close() {
                this.file = null;
                this.isRenaming = false;
                this.isVisible = false;

                if (!this.isPrompt) {
                    this.$store.dispatch('MediaLibrary/deactivateFile');
                }
            },

            invokeAction(action) {
                this.action = action;
                if (action === DETAIL_INVOCABLE_ACTIONS.RENAME) {
                    this.startRenaming();
                } else if (action === DETAIL_INVOCABLE_ACTIONS.OVERRIDE) {
                    this.selectFileReplacement();
                }
            },

            copyUrl() {
                this.$refs.urlField.select();
                document.execCommand('copy');
            },

            // RENAMING

            startRenaming() {
                this.isRenaming = true;

                if (this.isVisible) {
                    this.$nextTick(() => {
                        this.$refs.nameInput.focus();
                    });
                }
            },

            cancelRenaming() {
                if (this.locks.name) {
                    return;
                }

                this.isRenaming = false;
            },

            rename() {
                if (this.locks.name) {
                    return;
                }

                const value = this.$refs.nameInput.value.trim();

                if (!value.length) {
                    return;
                }

                if (value === this.file.getName()) {
                    this.cancelRenaming();
                    return;
                }

                this.locks.name = true;
                this.updateFile({name: value})
                    .then(() => {
                        this.locks.name = false;
                        this.cancelRenaming();
                    })
                    .finally(() => {
                        this.locks.name = false;
                    });
            },

            // UPDATES

            updateDescription() {
                if (this.locks.description) {
                    return;
                }

                const value = this.$refs.descriptionInput.value.trim();

                if (value === this.file.getDescription()) {
                    return;
                }

                this.locks.description = true;
                this.updateFile({description: value}).finally(() => this.locks.description = false);
            },

            rotateLeft() {
                if (this.locks.rotateLeft) {
                    return;
                }
                this.locks.rotateLeft = true;
                this.updateFile({rotate: 'left'}).finally(() => this.locks.rotateLeft = false);
            },

            rotateRight() {
                if (this.locks.rotateRight) {
                    return;
                }
                this.locks.rotateRight = true;
                this.updateFile({rotate: 'right'}).finally(() => this.locks.rotateRight = false);
            },

            /**
             * Update data of the file.
             *
             * @param {object} data
             * @returns {Promise}
             */
            updateFile(data) {
                return MediaService.updateFile(this.file, data).then(() => {
                    EventBus.$emit(EVENTS.FILE_UPDATED, this.file);
                });
            },

            onFullyVisible() {
                this.isVisible = true;

                if (this.action === DETAIL_INVOCABLE_ACTIONS.RENAME) {
                    this.startRenaming();
                }
            },

            remove() {
                if (this.locks.remove) {
                    return;
                }

                this.locks.remove = true;
                MediaService.deleteFilesWithConfirmation(this.file, this.localization)
                    .then(() => {
                        this.close();
                        this.$store.dispatch('MediaLibrary/deactivateFile', this.file);
                        EventBus.$emit(COMMANDS.FETCH_DIRECTORY_CONTENT);
                    })
                    .catch(() => {/* Delete not confirmed */
                    })
                    .finally(() => this.locks.remove = false);
            },

            selectFileReplacement() {
                if (this.locks.override) {
                    return;
                }

                this.$refs.fileInput.click();
            },

            fileReplacementSelected($event) {
                if (this.locks.override) {
                    return;
                }

                const files = $event.target.files || $event.dataTransfer.files;

                if (files.length !== 1) {
                    this.$refs.fileInput.value = [];
                    return;
                }

                this.locks.override = true;

                const url = MediaService.overrideFileUrl(this.file);

                new Uploader(url, files[0])
                    .onFinish(fileData => {
                        this.file.updateData(fileData);
                        EventBus.$emit(EVENTS.FILE_UPDATED, this.file);
                        this.locks.override = false;
                    })
                    .onFail(() => {
                        this.locks.override = false;
                    })
                    .start();

                this.$refs.fileInput.value = [];
            },

            resizeImage() {
                if (this.locks.resize) {
                    return;
                }

                this.$refs.imageReResizeModal.open(this.file)
                    .then((size) => {
                        this.locks.resize = true;
                        this.updateFile(size).finally(() => this.locks.resize = false);
                    })
                    .catch(() => {
                        // Closed
                    });
            },

            fileDeleted(file) {
                if (this.file && this.file.getId() === file.getId()) {
                    this.close();
                }
            }
        },

        created() {
            EventBus.$on(COMMANDS.SHOW_FILE_DETAIL, this.show);
            EventBus.$on(EVENTS.FILE_DELETED, this.fileDeleted);
        },

        destroyed() {
            EventBus.$off(COMMANDS.SHOW_FILE_DETAIL, this.show);
            EventBus.$off(EVENTS.FILE_DELETED, this.fileDeleted);
        },
    }
</script>

<style lang="scss" scoped>
    .close-button {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 2em;
        cursor: pointer;

        &:hover {
            color: #898989;
        }
    }

    .file-detail {
        position: absolute;
        background: white;
        width: 425px;
        height: 100%;
        top: 0;
        padding: 15px;
        border-left: 2px solid #EBEBEB;
        overflow-y: scroll;
        right: 0;
    }

    .filename {
        margin-bottom: 15px;

        div > .info {
            font-size: 1.4em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 300px;
            display: inline-block;
            vertical-align: bottom;
            padding-right: 5px;

            &:hover {
                border: 1px solid #ccc;
                border-radius: 3px;
            }
        }

        .form-group {
            max-width: 300px;
        }
    }

    .image-thumbnail {
        margin-bottom: 15px;
        background-color: #F5F5F5;

        > img {
            object-fit: scale-down;
        }
    }

    .info-header {
        display: block;
        color: #919191;
        text-transform: uppercase;
    }

    .info-container {
        margin-bottom: 15px;
    }

    .actions-heading {
        display: flex;
        align-items: center;

        &::after {
            content: "";
            display: block;
            flex: 1;
            margin-left: 1rem;
            height: 1px;
            background: #E9E9E9;
        }
    }

    .action-button {
        padding: 5px 0;

        > a {
            color: black;

            &:hover {
                color: #898989;
            }

            > i.fa {
                font-size: 1.3em;
                padding-right: 5px;
            }
        }
    }

    .slide-leave-active,
    .slide-enter-active {
        transition: right 0.5s;
    }

    .slide-enter, .slide-leave-to {
        right: -425px;
    }
</style>
