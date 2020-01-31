<template>
    <div class="cms-media-library">
        <media-header @search="search"
                      @create-directory="createDirectory"
                      @upload="selectFilesToUpload"
                      @delete-selected="deleteSelectedFiles"
                      @sort="changeSortOption"
                      @close="$emit('close')"
                      :can-delete="selectedFilesCount > 0"
                      :sort-options="sortOptions"
                      :active-sort-option="activeSortOption"
                      :enable-actions="Boolean(activeDirectory)"
                      :searched-text="searchText"
                      :is-prompt="isPrompt"
                      :localization="localization"
                      ref="mediaHeader"
                      :warn-cache-driver="warnCacheDriver"
        ></media-header>

        <tree-view :root="treeData"
                   class="cms-media-library__tree-view"
                   @rendered="onTreeRendered"
                   ref="treeView"
        ></tree-view>

        <folders-and-files v-if="directoryContent"
                           :content="directoryContent"
                           :uploads="directoryUploads"
                           :title="contentTitle"
                           :enable-dropzone="Boolean(activeDirectory)"
                           :file-type="fileType"
                           :multi-select="multiSelect"
                           :active-directory="activeDirectory"
                           @change-page="changePage"
                           :localization="localization"
                           ref="content"
        ></folders-and-files>

        <input type="file" multiple="multiple" @change="filesSelected" ref="fileInput" style="display: none">
    </div>
</template>


<script>
    import {EVENTS, COMMANDS, FILE_TYPE} from "./enums";
    import MediaHeader from "./components/media-header";
    import TreeView from "./components/tree-view";
    import FoldersAndFiles from "./components/folders-and-files";
    import Uploader from "./uploader";
    import MediaFile from "./models/MediaFile";
    import MediaService from "./service";
    import LocalizationMixin from "../vue-mixins/localization";

    const sortOptions = [
        {
            by: 'name',
            direction: 'ASC'
        },
        {
            by: 'name',
            direction: 'DESC'
        },
        {
            by: 'updated_at',
            direction: 'ASC'
        },
        {
            by: 'updated_at',
            direction: 'DESC'
        }
    ];

    export default {
        mixins: [LocalizationMixin],

        components: {
            MediaHeader,
            TreeView,
            FoldersAndFiles
        },

        data() {
            return {
                treeData: null,
                activeDirectory: null,
                previouslyActiveDirectory: null,
                directoryContent: null,
                selectedFiles: {},
                uploads: [],
                uploadingCount: 0,
                sortOptions: sortOptions,
                activeSortOption: this.sortBy ? {
                    by: this.sortBy,
                    direction: this.sortDir ? this.sortDir.toUpperCase() : 'ASC'
                } : sortOptions[0],
                searchText: null,
                activeFile: null,
                page: 1
            };
        },

        props: {
            fileType: {
                type: [String, Array],
                default: FILE_TYPE.ANY
            },

            multiSelect: {
                type: Boolean,
                default: true
            },

            isPrompt: {
                type: Boolean,
                default: false
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
            /**
             * Fetch directory tree.
             */
            fetchDirectoryTree() {
                MediaService.fetchDirectoryTree().then(tree => this.treeData = tree);
            },

            /**
             * Create new directory prompt and request.
             */
            createDirectory() {
                if (!this.activeDirectory) {
                    return;
                }

                const name = prompt(
                    this.localization.trans('directories.prompt_create_text'),
                    this.localization.trans('directories.default_folder_name')
                );
                if (name === null || name === "") {
                    return;
                }

                MediaService.createDirectory(this.activeDirectory, {name: name})
                    .then(data => {
                        EventBus.$emit(COMMANDS.UPDATE_DIRECTORY_CONTENT, this.activeDirectory.id, data.subTree);

                        // after rendering we emit event to activate this new directory.
                        this.$nextTick(() => {
                            EventBus.$emit(COMMANDS.ACTIVATE_DIRECTORY, data.directory.id);
                        });
                    });
            },

            /**
             * Triggered when directory is activated.
             */
            directoryActivated(directory) {
                if (this.directoryContent !== null) {
                    this.page = 1;
                }

                this.activeDirectory = directory;

                if (directory) {
                    this.fetchDirectoryContent();
                }
            },

            /**
             * Triggered when tree view is rendered.
             */
            onTreeRendered() {
                if (this.isPrompt) {
                    EventBus.$emit(COMMANDS.ACTIVATE_DIRECTORY, null);
                } else {
                    this.initializeByUrl();
                }
            },

            /**
             * Fetch content of the active directory.
             */
            fetchDirectoryContent() {
                this.resetSearch();

                MediaService.fetchDirectoryContent(this.activeDirectory, {
                    sort: this.activeSortOption.by,
                    dir: this.activeSortOption.direction,
                    type: this.fileType,
                    page: this.page
                }).then(this.handleContentResponse);
            },

            // UPLOAD

            /**
             * Select files to upload.
             */
            selectFilesToUpload() {
                if (!this.activeDirectory) {
                    return;
                }

                this.$refs.fileInput.click();
            },

            /**
             * Triggered when user select files to upload.
             * @param $event
             */
            filesSelected($event) {
                const files = $event.target.files || $event.dataTransfer.files;

                if (!files.length) {
                    return;
                }

                this.uploadFiles(files);

                this.$refs.fileInput.value = [];
            },

            /**
             * Upload given files.
             * @param {File[]} files
             */
            uploadFiles(files) {
                for (let i = files.length - 1; i >= 0; i--) {
                    const file = files[i];

                    // skip folders
                    if (!file.type && file.size % 4096 === 0) {
                        continue;
                    }

                    this.createUpload(files[i]);
                }
            },

            /**
             * Create object to upload file.
             * @param {File} file
             */
            createUpload(file) {
                const url = MediaService.fileUploadUrl(this.activeDirectory);

                this.uploads.push({
                    uploader: new Uploader(url, file),
                    isReserved: false,
                    file: null,
                    directoryId: this.activeDirectory.id || null
                });

                this.startNextUpload();
            },

            /**
             * Start uploading next file.
             */
            startNextUpload() {
                if (this.uploadingCount >= 1) {
                    return;
                }

                const upload = this.uploads.find(upload => !upload.isReserved);

                if (!upload) {
                    return;
                }

                this.uploadingCount++;
                upload.isReserved = true;

                upload.uploader.start()
                    .onFinish(file => {
                        upload.uploader = null;
                        upload.file = new MediaFile(file);

                        // Decrement uploading count and start next upload.
                        this.uploadingCount--;
                        this.startNextUpload();
                    })
                    .onCancel(() => {
                        const index = this.uploads.indexOf(upload);
                        if (index !== -1) {
                            this.uploads.splice(index, 1);
                        }

                        this.uploadingCount--;
                        this.startNextUpload();
                    })
                    .onFail(() => {
                        this.uploadingCount--;
                        this.startNextUpload();
                    });
            },

            // SELECTING

            /**
             * Triggered when file is selected.
             * @param {MediaFile} file
             */
            fileSelected(file) {
                this.selectedFiles[file.getId()] = file;
                this.selectedFiles = {...this.selectedFiles};
            },

            /**
             * Triggered when file is unselected.
             * @param {MediaFile} file
             */
            fileUnselected(file) {
                delete this.selectedFiles[file.getId()];
                this.selectedFiles = {...this.selectedFiles};
            },

            // DELETING

            /**
             * Deletes selected files.
             */
            deleteSelectedFiles() {
                const files = Object.values(this.selectedFiles);
                if (!files.length) {
                    return;
                }

                MediaService.deleteFilesWithConfirmation(files, this.localization)
                    .then(() => {
                        this.page -= Math.floor(this.selectedFilesCount / this.directoryContent.files.per_page);

                        if (this.page < 1) {
                            this.page = 1;
                        }

                        this.selectedFiles = {};
                        this.fetchDirectoryContent();
                    })
                    .catch(() => {/* Delete not confirmed */});
            },

            // SORTING

            /**
             * Triggered when sort option is being changed.
             */
            changeSortOption(option) {
                this.activeSortOption = option;
                this.$emit('sort', option);

                if (this.searchText === null) {
                    this.fetchDirectoryContent();
                } else {
                    this.search(this.searchText);
                }
            },

            // SEARCHING

            /**
             * Reset search.
             */
            resetSearch() {
                this.searchText = null;
                this.$refs.mediaHeader.resetSearch();
            },

            /**
             * Search given text.
             * @param {string|null} text
             */
            search(text) {
                this.searchText = text;

                if (text === null) {
                    EventBus.$emit(COMMANDS.ACTIVATE_DIRECTORY, this.previouslyActiveDirectory ? this.previouslyActiveDirectory.id : null);
                    return;
                }

                if (this.activeDirectory) {
                    this.previouslyActiveDirectory = this.activeDirectory;
                    this.page = 1;
                    EventBus.$emit(EVENTS.DIRECTORY_ACTIVATED, null);
                }

                MediaService.search({
                    sort: this.activeSortOption.by,
                    dir: this.activeSortOption.direction,
                    query: text,
                    page: this.page
                }).then(this.handleContentResponse);
            },

            // Handlers

            /**
             * Handle response with actual content.
             *
             * @param {object} response
             */
            handleContentResponse(response) {
                this.uploads = this.uploads.filter(upload => upload.uploader !== null);

                this.directoryContent = {
                    directories: response.data.directories,
                    files: {
                        ...response.data.files,
                        data: response.data.files.data.map(fileData => {
                            const file = new MediaFile(fileData);

                            // Select file if should be selected
                            file.data.isSelected = this.selectedFiles.hasOwnProperty(file.getId());

                            // Activate file if should be active
                            file.data.isActive = this.activeFile && file.getId() === this.activeFile.getId();

                            return file;
                        })
                    }
                };

                if (this.page > response.data.files.last_page) {
                    this.page = response.data.files.last_page;
                    return this.fetchDirectoryContent();
                } else {
                    this.page = response.data.files.current_page;
                }

                if (!this.isPrompt) {
                    this.pushHistoryState({
                        directoryId: this.activeDirectoryId,
                        sort: this.activeSortOption,
                        query: response.config.params.query || null,
                        page: this.page
                    });
                }
            },

            changePage(page) {
                this.page = page;

                if (this.searchText !== null) {
                    this.search(this.searchText);
                } else {
                    this.fetchDirectoryContent();
                }
            },

            fileDeleted(file) {
                if (this.$store.getters['MediaLibrary/activeFileId'] === file.getId()) {
                    this.$store.dispatch('MediaLibrary/deactivateFile');
                }

                if (this.selectedFiles[file.getId()]) {
                    this.fileUnselected(file);
                }
            },

            // Commands

            /**
             * Activate given file.
             * @param {MediaFile} file
             */
            activateFile(file) {
                this.activeFile = file;
            },

            /**
             * Select given files.
             * @param {MediaFile[]} files
             */
            selectFiles(files) {
                this.selectedFiles = {};
                files.map(file => this.selectedFiles[file.getId()] = file);
            },

            // History and url manipulation

            /**
             * Push new history state.
             *
             * @param {object} params
             */
            pushHistoryState(params) {
                const url = [];

                if (params.query !== null) {
                    url.push(`query=${encodeURIComponent(params.query)}`);
                } else if (params.directoryId) {
                    url.push(`directory=${params.directoryId}`);
                }

                if (params.sort) {
                    url.push(`sort=${params.sort.by}&dir=${params.sort.direction}`);
                }

                if (this.page !== 1) {
                    url.push(`page=${this.page}`);
                }

                window.history.pushState(params, null, url.length ? '#!' + url.join('&') : '');
            },

            /**
             * Refresh state when browser history state changes.
             *
             * @param {PopStateEvent} event
             */
            onPopState(event) {
                if (!event.state) {
                    window.location.reload();
                    return;
                }

                if (event.state.sort) {
                    this.activeSortOption = event.state.sort;
                }

                if (event.state.page) {
                    this.page = event.state.page;
                }

                if (event.state.query) {
                    this.search(event.state.query);
                } else {
                    EventBus.$emit(COMMANDS.ACTIVATE_DIRECTORY, event.state.directoryId);
                }
            },

            /**
             * Initializes media library by parameters in url.
             */
            initializeByUrl() {
                const params = {};
                const hash = window.location.hash || '';
                if (hash.substr(0, 2) === '#!') {
                    hash.substring(2).split('&').map(param => {
                        const keyVal = param.split('=');
                        if (keyVal.length === 2) {
                            params[keyVal[0]] = keyVal[1];
                        }
                    });
                }

                if (params.sort) {
                    this.sortOptions.some(option => {
                        if (option.by === params.sort && option.direction === params.dir) {
                            this.activeSortOption = option;
                            return true;
                        }

                        return false;
                    });
                }

                if (params.page) {
                    this.page = Number(params.page);
                }

                if (params.query) {
                    this.$refs.mediaHeader.setSearch(params.query);
                    this.search(params.query);
                } else {
                    EventBus.$emit(COMMANDS.ACTIVATE_DIRECTORY, Number(params.directory) || null);
                }
            }
        },

        computed: {
            directoryUploads() {
                return this.uploads.filter(upload => upload.directoryId === this.activeDirectoryId);
            },

            activeDirectoryId() {
                return this.activeDirectory ? this.activeDirectory.id || null : null;
            },

            contentTitle() {
                if (this.searchText !== null) {
                    return this.localization.trans('search_title', {phrase: this.searchText});
                }

                return this.activeDirectory.name;
            },

            selectedFilesCount() {
                return Object.keys(this.selectedFiles).length;
            }
        },

        updated() {
            if (this.$refs.content) {
                const height = this.$refs.content.$el.clientHeight;
                this.$refs.treeView.$el.style.minHeight = `${height}px`;
            }
        },

        created() {
            if (this.isPrompt) {
                this.$store.commit('MediaLibrary/makePrompt');
            }

            EventBus.$on(COMMANDS.UPLOAD_FILES, this.uploadFiles);
            EventBus.$on(COMMANDS.FETCH_DIRECTORY_CONTENT, this.fetchDirectoryContent);

            EventBus.$on(EVENTS.DIRECTORY_ACTIVATED, this.directoryActivated);
            EventBus.$on(EVENTS.FILE_SELECTED, this.fileSelected);
            EventBus.$on(EVENTS.FILE_UNSELECTED, this.fileUnselected);
            EventBus.$on(EVENTS.FILE_DELETED, this.fileDeleted);

            if (!this.isPrompt) {
                window.addEventListener('popstate', this.onPopState);
            }

            this.fetchDirectoryTree();
        },

        beforeDestroy() {
            EventBus.$off(COMMANDS.UPLOAD_FILES, this.uploadFiles);
            EventBus.$off(COMMANDS.FETCH_DIRECTORY_CONTENT, this.fetchDirectoryContent);

            EventBus.$off(EVENTS.DIRECTORY_ACTIVATED, this.directoryActivated);
            EventBus.$off(EVENTS.FILE_SELECTED, this.fileSelected);
            EventBus.$off(EVENTS.FILE_UNSELECTED, this.fileUnselected);
            EventBus.$off(EVENTS.FILE_DELETED, this.fileDeleted);

            if (!this.isPrompt) {
                window.removeEventListener('popstate', this.onPopState);
            }
        }
    };
</script>

<style lang="scss" scoped>
    .cms-media-library {
        position: relative;
        height: 100%;
        border-bottom: 1px solid #D8D8D8;
    }

    .cms-media-library__tree-view {
        overflow: auto;
        background-color: white;
        border-right: 1px solid #D8D8D8;
        width: 200px;
        float:left;
    }

    ul {
        padding-left: 1em;
        line-height: 1.5em;
        list-style-type: dot;
    }
</style>
