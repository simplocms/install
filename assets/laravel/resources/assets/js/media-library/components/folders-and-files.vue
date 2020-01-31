<template>
    <div class="cms-media-library__files-and-folders" v-if="content" id="cms-media-library-dropzone">
        <div class="inner">
            <div class="content-header">
                <h2><i class="fa fa-level-up"></i> {{ title }}</h2>

                <!-- Directory menu -->
                <div @click="openSettingsMenu" class="directory-settings" v-if="isDirectory">
                    <span></span>
                    <span></span>
                    <span></span>

                    <div v-if="isSettingsMenuOpen">
                        <ul class="directory-settings-menu">
                            <li @click.prevent="renameDirectory">
                                <i class="fa fa-pencil"></i> {{ localization.trans('directories.btn_rename') }}
                            </li>
                            <li @click.prevent="deleteDirectory">
                                <i class="fa fa-trash-o"></i> {{ localization.trans('directories.btn_delete') }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Directories -->
            <div class="cms-media-library__accordion" v-if="content.directories.length">
                <div class="cms-media-library__accordion-heading" @click.prevent="showDirectories = !showDirectories">
                    <i class="fa fa-chevron-down" v-if="showDirectories"></i>
                    <i class="fa fa-chevron-right" v-else></i>
                    {{ localization.trans('subdirectories_text') }} ({{ content.directories.length }})
                </div>
                <div class="cms-media-library__accordion-content" v-if="showDirectories">
                    <div class="cms-media-library__folders">
                        <content-directory v-for="directory in content.directories"
                                           :key="directory.id"
                                           :directory="directory"
                        ></content-directory>
                    </div>
                </div>
            </div>

            <!-- Files -->
            <div class="cms-media-library__accordion">
                <div class="cms-media-library__accordion-heading" @click.prevent="showFiles = !showFiles">
                    <i class="fa fa-chevron-down" v-if="showFiles"></i>
                    <i class="fa fa-chevron-right" v-else></i>
                    {{ localization.trans('files_text') }} ({{ filesCount }})
                </div>
                <div class="cms-media-library__accordion-content" v-if="showFiles">
                    <div class="cms-media-library__folders">
                        <!-- Uploads -->
                        <content-file v-for="(upload, index) in uploads"
                                      :key="'upload-' + index"
                                      :uploader="upload.uploader"
                                      :file="upload.file"
                                      :multi-select="multiSelect"
                                      v-show="imagesOnly ? !upload.file || upload.file.isImage() : true"
                                      :localization="localization"
                        ></content-file>

                        <!-- Existing files -->
                        <content-file v-if="content.files.total"
                                      v-for="file in content.files.data"
                                      :key="file.getId()"
                                      :file="file"
                                      :multi-select="multiSelect"
                                      :localization="localization"
                        ></content-file>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="text-center">
                <pagination :per-page="content.files.per_page"
                            :total="content.files.total"
                            :value="content.files.current_page"
                            v-show="content.files.last_page > 1"
                            @change="$emit('change-page', $event)"
                ></pagination>
            </div>
        </div>

        <!-- File detail -->
        <file-detail :localization="localization"></file-detail>

        <!-- File drop -->
        <file-drop v-if="enableDropzone" :localization="localization"></file-drop>
    </div>
</template>


<script>
    import ContentDirectory from "./content-directory";
    import ContentFile from "./content-file";
    import FileDetail from "./file-detail";
    import FileDrop from "./file-drop";
    import {COMMANDS, FILE_TYPE} from "../enums";
    import MediaService from "../service";
    import Pagination from '../../vue-components/pagination';

    export default {
        components: {
            ContentDirectory,
            ContentFile,
            FileDetail,
            FileDrop,
            Pagination
        },

        data() {
            return {
                showDirectories: true,
                showFiles: true,
                isSettingsMenuOpen: false
            };
        },

        props: {
            content: {
                type: Object,
                default: null
            },
            uploads: {
                type: Array,
                default: () => []
            },
            title: String,
            enableDropzone: Boolean,
            fileType: [String, Array],
            multiSelect: Boolean,
            activeDirectory: Object,
            localization: Object,
        },

        computed: {
            imagesOnly() {
                return this.fileType === FILE_TYPE.IMAGE;
            },

            isDirectory() {
                return this.activeDirectory && this.activeDirectory.id;
            },

            filesCount() {
                const uploadsCount = this.uploads.filter(upload => upload.file !== null).length;
                if (!this.content) {
                    return 0;
                }

                return this.content.files.total + uploadsCount;
            }
        },

        methods: {
            closeSettingsMenu() {
                this.isSettingsMenuOpen = false;
                document.removeEventListener('click', this.closeSettingsMenu);
            },

            openSettingsMenu() {
                if (!this.isSettingsMenuOpen) {
                    this.$nextTick(() => {
                        document.addEventListener('click', this.closeSettingsMenu);
                    });
                }

                this.isSettingsMenuOpen = true;
            },

            renameDirectory() {
                if (!this.isDirectory) {
                    return;
                }

                const name = prompt(
                    this.localization.trans('directories.prompt_rename_text'), this.activeDirectory.name
                );
                if (name === null || name === "" || name.trim() === this.activeDirectory.name) {
                    return;
                }

                MediaService.updateDirectory(this.activeDirectory, {name: name})
                    .then(data => {
                        this.activeDirectory.name = data.directory.name;
                        EventBus.$emit(COMMANDS.FETCH_DIRECTORY_CONTENT);
                    });
            },

            deleteDirectory() {
                if (!this.isDirectory) {
                    return;
                }

                const deleteId = this.activeDirectory.id;

                MediaService.deleteDirectoryWithConfirmation(this.activeDirectory, this.localization)
                    .then(response => {
                        EventBus.$emit(COMMANDS.ACTIVATE_DIRECTORY, response.parent_id);

                        // Wait for active directory to change
                        this.$nextTick(() => {
                            const index = this.activeDirectory.children.findIndex(child => child.id === deleteId);
                            if (index !== -1) {
                                this.activeDirectory.children.splice(index, 1);
                            }
                        })
                    })
                    .catch(() => {})
            }
        }
    };
</script>


<style lang="scss" scoped>
    .cms-media-library__files-and-folders {
        position: relative;
        overflow: hidden;
        background-color: #F5F5F5;
        min-height: 600px;

        > .inner {
            height: 100%;
            min-height: 600px;
            overflow-y: auto;
        }
    }

    .cms-media-library__folders {
        margin: 10px 0;
        display: flex;
        flex-wrap: wrap;
    }

    .cms-media-library__accordion {
        margin: 2rem;
    }

    .cms-media-library__accordion-heading {
        display: flex;
        align-items: center;
        cursor: pointer;
        text-transform: uppercase;
        font-size: 0.9em;

        > .fa {
            margin-right: 1rem;
        }

        &::after {
            content: "";
            display: block;
            flex: 1;
            margin-left: 1rem;
            height: 1px;
            background: #DEDEDE;
        }
    }

    .content-header {
        position: relative;

        h2 {
            margin: 2rem;
            font-size: 1.8em;

            > i.fa {
                padding-right: 5px;
            }
        }
    }

    .directory-settings {
        position: absolute;
        right: 2rem;
        top: 0.5rem;
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

    .directory-settings-menu {
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
</style>
