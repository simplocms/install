<template>
    <div class="clearfix file-selector" v-if="!multiple" :class="{well: !(image || video)}">
        <!-- Image preview -->
        <div class="thumbnail display-inline-block pull-left" v-if="image">
            <img :src="singleImagePreviewUrl"
                 :alt="$root.localization.trans('file_selector.image_preview')"
            >

            <div class="element-lock-hover" v-show="isLoading">
                <div class="lock-inner"><i class="fa fa-spinner fa-spin"></i></div>
            </div>
        </div>

        <!-- Video preview -->
        <div class="thumbnail display-inline-block pull-left" v-else-if="video">
            <video width="140" height="110" controls v-if="singleFileSelected">
                <source :src="singleFile.getUrl()" :type="singleFile.getMimeType()">
                {{ $root.localization.trans('file_selector.video_not_supported') }}
            </video>

            <img v-else
                 src="/media/admin/images/image_placeholder.png"
                 alt="video"
            >

            <div class="element-lock-hover" v-show="isLoading">
                <div class="lock-inner"><i class="fa fa-spinner fa-spin"></i></div>
            </div>
        </div>

        <div class="file-name" :class="{'text-italic': !singleFileSelected}" v-else>
            <div class="element-lock-hover" v-show="isLoading">
                <div class="lock-inner"><i class="fa fa-spinner fa-spin"></i></div>
            </div>
             {{ singleFileText }}
        </div>

        <div :class="[(image || video) ? 'col-xs-6' : 'inline-block']">
            <button class="btn btn-primary mb-10"
                    type="button"
                    @click="selectSingleFile"
            >
                {{ singleSelectButtonText }}
            </button>

            <button class="btn btn-default mb-10"
                    type="button"
                    v-show="singleFileSelected"
                    @click="clearSelection"
            >
                {{ singleClearButtonText }}
            </button>

            <small class="allowed-types text-muted" v-if="fileType">
                {{ $root.localization.trans('file_selector.accepted') }} {{ fileType }}
            </small>

            <small class="help-block text-muted" v-if="video">
                {{ $root.localization.trans('file_selector.video_support_notice') }}
            </small>

            <span class="help-block" v-show="error" :data-for="inputName">{{ error }}</span>

            <input type="hidden" :value="singleFileId" :name="inputName"/>
        </div>
    </div>
</template>

<style lang="scss" scoped>
    .file-selector {
        .thumbnail {
            margin-bottom: 0;

            > img, > video {
                max-width: 140px;
                max-height: 110px;
            }
        }

        &.well {
            padding: 10px;
        }

        .file-name {
            padding-bottom: 10px;
        }

        .allowed-types {
            float: right;
            margin-top: 17px;
        }
    }

    .has-error .thumbnail {
        border-color: #a94442;
    }
</style>

<script>
    import MediaFile from "../models/MediaFile";

    export default {
        data() {
            return {
                singleFilePreviewUrl: null,
                singleFile: null,
                isLoading: false
            };
        },

        props: {
            image: {
                type: Boolean,
                default: true
            },
            video: {
                type: Boolean,
                default: false
            },
            multiple: {
                type: Boolean,
                default: false
            },
            fileType: {
                type: [String, Array],
                default: null
            },
            error: String,
            inputName: String,
            value: Number,
        },

        methods: {
            selectSingleFile() {
                let prompt;

                if (this.image) {
                    prompt = window.MediaLibraryPrompt.singleImage();
                } else if (this.video) {
                    prompt = window.MediaLibraryPrompt.singleVideo();
                } else {
                    prompt = window.MediaLibraryPrompt.singleFile();
                }

                if (this.fileType) {
                    prompt.fileType(this.fileType);
                }

                prompt.selectFiles(this.singleFile)
                    .open()
                    .then(this.setSingleFile, () => {
                        // no image selected
                    });
            },

            setSingleFile(file) {
                let mediaFile = file ? new MediaFile(file) : null;

                // If file changed to non-image.
                if (mediaFile && this.image && !mediaFile.isImage()) {
                    mediaFile = null;
                }

                this.singleFile = mediaFile;
                this.$emit('input', this.singleFile ? this.singleFile.getId() : null);
                this.$emit('change', this.singleFile);
            },

            fetchSingleFile(fileId) {
                this.isLoading = true;
                Request.get(`/admin/media-library/files/${fileId}`)
                    .done(response => {
                        this.setSingleFile(response.file);
                    })
                    .catch(thrown => {
                        this.setSingleFile(null);
                    })
                    .always(() => {
                        this.isLoading = false;
                    })
            },

            clearSelection() {
                this.setSingleFile(null);
            },

            fileUpdated(file) {
                if (this.singleFile && file.getId() === this.singleFile.getId()) {
                    this.setSingleFile(file);
                }
            },

            setValue(file) {
                if (typeof file === 'object' || file instanceof MediaFile || file === null) {
                    return this.setSingleFile(file);
                }

                if (typeof file === 'number' && (!this.singleFile || this.singleFile.getId() !== file)) {
                    return this.fetchSingleFile(file);
                }
            }
        },

        computed: {
            singleFileId() {
                return this.singleFileSelected ? this.singleFile.getId() : null;
            },

            singleImagePreviewUrl() {
                return this.singleFileSelected ?
                    this.singleFile.getPreview().fitToCanvas(140, 100).preview().getUrl()
                    : '/media/admin/images/image_placeholder.png';
            },

            singleFileSelected() {
                return this.singleFile !== null;
            },

            singleSelectButtonText() {
                if (this.image) {
                    return this.singleFileSelected ? this.$root.localization.trans('file_selector.btn_change_image')
                        : this.$root.localization.trans('file_selector.btn_select_image');
                }

                return this.singleFileSelected ? this.$root.localization.trans('file_selector.btn_change_file')
                    : this.$root.localization.trans('file_selector.btn_select_file');
            },

            singleClearButtonText() {
                return this.image ? this.$root.localization.trans('file_selector.btn_remove_image')
                    : this.$root.localization.trans('file_selector.btn_remove_file');
            },

            singleFileText() {
                return this.singleFileSelected ? this.singleFile.getNameWithExtension()
                    : this.$root.localization.trans('file_selector.no_file_selected');
            }
        },

        watch: {
            value: {
                handler(file) {
                    // this.singleFile = file ? new MediaFile(file) : null;
                    this.setValue(file);
                },
                immediate: true
            }
        },

        created() {
            EventBus.$on('media-library::file-updated', this.fileUpdated);

            // if (this.value && !this.multiple) {
            //     this.setSingleFile(this.value);
            // }
        },

        destroyed() {
            EventBus.$off('media-library::file-updated', this.fileUpdated);
        }
    };
</script>
