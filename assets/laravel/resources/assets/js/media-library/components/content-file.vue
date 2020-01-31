<template>
    <div class="cms-media-library__file" :class="{active: isActive, selected: isSelected}">

        <!-- Upload preview -->
        <div class="cms-media-library__file-thumb" v-if="isUploader">
            <div v-if="!uploadFailed">
                <div class="progress">
                    <div class="progress-bar progress-bar-striped active"
                         :style="{width: uploadProgress + '%'}"
                    ></div>
                </div>
                <div class="text-center">{{ localization.trans('file.uploading') }} ({{ uploadProgress }} %)</div>
            </div>
            <div class="upload-error text-danger" v-else>
                {{ localization.trans('file.upload_failed') }}
                <br>
                <a href="#" @click.prevent="retryUpload">{{ localization.trans('file.btn_upload_again') }}</a>
            </div>
        </div>

        <!-- File preview -->
        <div class="cms-media-library__file-thumb" v-else>
            <!-- Image preview -->
            <img :src="imagePreviewUrl" :alt="file.getName()"
                 @click="activateFile"
                 @error="imagePreviewFailedToLoad"
                 v-if="hasPreview"
                 draggable="false"
            >

            <!-- File icon -->
            <div class="file-icon" @click="activateFile" v-else><i class="fa fa-file-o"></i></div>

            <!-- Checkbox for selecting and button for detail -->
            <span class="hover-checkbox" :class="{checked: isSelected}" @click.prevent="toggleSelect">
                <i class="fa fa-check"></i>
            </span>
            <button class="btn btn-xs bg-teal-400 hover-button" @click.prevent="showDetail()">
                {{ localization.trans('file.btn_detail') }}
            </button>
        </div>

        <div class="cms-media-library__file-info">
            <div class="cms-media-library__file-name" :title="fileName">
                {{ fileName }}
            </div>
            <div class="cms-media-library__file-meta">
                {{ fileInfoText }}
            </div>

            <div @click="openControlMenu" class="cms-media-library__settings">
                <span></span>
                <span></span>
                <span></span>

                <div v-if="isControlMenuOpen">
                    <ul class="cms-media-library__settings-menu" v-if="isUploader">
                        <li v-show="uploadFailed" @click.prevent="retryUpload">
                            <i class="fa fa-refresh"></i> {{ localization.trans('file.btn_upload_again') }}
                        </li>
                        <li @click.prevent="cancelUpload">
                            <i class="fa fa-trash-o"></i> {{ localization.trans('file.btn_cancel_upload') }}
                        </li>
                    </ul>
                    <ul class="cms-media-library__settings-menu" v-else>
                        <li @click.prevent="showDetail()">
                            <i class="fa fa-search"></i> {{ localization.trans('file.btn_detail') }}
                        </li>
                        <li @click.prevent="toggleSelect">
                            <i class="fa fa-check"></i> {{ localization.trans('file.btn_select') }}
                        </li>
                        <li @click.prevent="rename">
                            <i class="fa fa-pencil"></i> {{ localization.trans('file.btn_rename') }}
                        </li>
                        <li @click.prevent="override">
                            <i class="fa fa-upload"></i> {{ localization.trans('file.btn_override') }}
                        </li>
                        <li @click.prevent="remove">
                            <i class="fa fa-trash-o"></i> {{ localization.trans('file.btn_delete') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import MediaFile from "../models/MediaFile";
    import {EVENTS, COMMANDS, DETAIL_INVOCABLE_ACTIONS} from "../enums";
    import Uploader from "../uploader";
    import MediaService from "../service";

    export default {
        props: {
            file: {
                type: MediaFile,
                default: null
            },

            uploader: {
                type: Uploader,
                default: null
            },

            multiSelect: Boolean,

            localization: Object
        },

        data() {
            return {
                isControlMenuOpen: false,
                isSelected: this.file ? this.file.data.isSelected : false,
                uploadProgress: 0,
                uploadFailed: false,
                previewFailed: false,
            };
        },

        methods: {
            activateFile() {
                if (!this.isUploader) {
                    this.$store.dispatch('MediaLibrary/activateFile', this.file);

                    if (!this.multiSelect) {
                        EventBus.$emit(EVENTS.FILE_SELECTION_CONFIRMED, this.file);
                    } else {
                        this.showDetail();
                    }
                }
            },

            closeControlMenu() {
                this.isControlMenuOpen = false;
                document.removeEventListener('click', this.closeControlMenu);
            },

            openControlMenu() {
                if (!this.isControlMenuOpen) {
                    this.$nextTick(() => {
                        document.addEventListener('click', this.closeControlMenu);
                    });
                }

                this.isControlMenuOpen = true;
            },

            toggleSelect() {
                if (this.isSelected) {
                    this.unselectFile();
                } else {
                    this.selectFile();
                }
            },

            selectFile() {
                this.isSelected = true;
                EventBus.$emit(EVENTS.FILE_SELECTED, this.file);
            },

            unselectFile() {
                this.isSelected = false;
                EventBus.$emit(EVENTS.FILE_UNSELECTED, this.file);
            },

            showDetail(action = DETAIL_INVOCABLE_ACTIONS.NONE) {
                EventBus.$emit(COMMANDS.SHOW_FILE_DETAIL, this.file, action);
            },

            rename() {
                this.showDetail(DETAIL_INVOCABLE_ACTIONS.RENAME);
            },

            override() {
                this.showDetail(DETAIL_INVOCABLE_ACTIONS.OVERRIDE);
                this.$nextTick(() => {
                    this.closeControlMenu();
                });
            },

            retryUpload() {
                this.uploader.retry();
                this.uploadProgress = 0;
                this.uploadFailed = false;
            },

            cancelUpload() {
                this.uploader.cancel();
            },

            remove() {
                MediaService.deleteFilesWithConfirmation(this.file, this.localization)
                    .then(() => {
                        EventBus.$emit(COMMANDS.FETCH_DIRECTORY_CONTENT);
                    })
                    .catch(() => {/* Delete not confirmed */});
            },

            imagePreviewFailedToLoad() {
                this.previewFailed = true;
            }
        },

        computed: {
            isImage() {
                return this.file && this.file.isImage();
            },

            isUploader() {
                return this.uploader !== null;
            },

            fileName() {
                if (this.isUploader) {
                    return this.uploader.getFilename();
                }

                return this.file.getNameWithExtension();
            },

            fileInfoText() {
                if (this.isUploader) {
                    return this.uploader.getFileSize();
                }

                let text = this.file.getHumanSize();
                if (this.isImage && this.file.isResolutionAvailable()) {
                    text += ` | ${this.file.getResolutionText()}`;
                }

                return text;
            },

            isActive() {
                return this.file && this.file.getId() === this.$store.getters['MediaLibrary/activeFileId'];
            },

            hasPreview() {
                return !this.previewFailed && !this.isUploader && this.file.hasPreview();
            },

            imagePreviewUrl() {
                if (this.hasPreview) {
                    return this.file.getPreview().fitToCanvas(200, 140).preview().getUrl();
                }

                return null;
            }
        },

        created() {
            if (this.isUploader) {
                this.uploader.onProgress(progress => this.uploadProgress = progress)
                    .onFail(() => {
                        this.uploadFailed = true;
                    });
            }
        },
    };
</script>

<style lang="scss" scoped>
    .cms-media-library__file {
        background: #fff;
        border-radius: 3px;
        border: 1px solid rgba(0, 0, 0, 0.12);
        margin: 5px 2.5px;
        padding: 5px;
        position: relative;
        width: 200px;

        &:hover {
            border-color: #C0C0C0;
        }

        &.active {
            border-color: #26A69A;
        }
    }

    .cms-media-library__file-thumb {
        border-radius: 3px;
        overflow: hidden;
        height: 140px;
        position: relative;
        background-color: #F5F5F5;

        img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            cursor: pointer;
        }

        .hover-button {
            position: absolute;
            top: 5px;
            right: 5px;
            visibility: hidden;
        }

        .hover-checkbox {
            position: absolute;
            top: 5px;
            left: 5px;
            font-size: 1.2em;
            background: white;
            height: 20px;
            width: 20px;
            border: 2px solid #707070;
            border-radius: 4px;
            line-height: 19px;
            cursor: pointer;
            visibility: hidden;

            > i.fa {
                visibility: hidden;
            }

            &.checked > i.fa {
                visibility: visible;
            }

            &:hover:not(.checked) > i.fa {
                visibility: visible;
                opacity: 0.2;
            }
        }

        &:hover {
            .hover-button, .hover-checkbox {
                visibility: visible;
            }
        }

        .progress {
            margin: 50px 10px 5px;
            height: 10px;
        }

        .upload-error {
            text-align: center;
            margin-top: 25%;
        }

        .file-icon {
            font-size: 5em;
            padding: 25px 65px;
            color: #C3C3C3;
            cursor: pointer;
        }
    }

    .cms-media-library__file-info {
        padding: 5px;
        padding-bottom: 0;
    }

    .cms-media-library__file-name {
        font-size: 1.3rem;
        color: #2f2f2f;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 160px;
    }

    .cms-media-library__file-meta {
        font-size: 1rem;
        color: rgba(47, 47, 47, 0.6);
    }

    .cms-media-library__settings {
        position: absolute;
        right: 0.5rem;
        bottom: 1.5rem;
        cursor: pointer;
        padding: 4px 10px;

        > span {
            width: 3px;
            height: 3px;
            border-radius: 50%;
            border: 1px solid rgba(0, 0, 0, 0.6);
            display: block;
            background: black;
            margin-bottom: 2px;
        }
    }

    .cms-media-library__settings-menu {
        position: absolute;
        top: 100%;
        right: 0;
        width: 165px;
        background: #fff;
        z-index: 99;
        padding: 0;
        border-radius: 5px;
        list-style: none;
        border: 1px solid #ddd;

        > li {
            padding: 8px 10px;

            &:hover {
                background-color: #F5F5F5;
            }

            > i.fa {
                font-size: 1.2em;
                padding-right: 5px;
            }
        }
    }

    .cms-media-library__file.selected > .cms-media-library__file-thumb > .hover-checkbox {
        visibility: visible;
    }
</style>

