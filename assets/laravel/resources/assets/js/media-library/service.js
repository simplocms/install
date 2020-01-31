import {EVENTS} from "./enums";

export default class MediaService {
    /**
     * Fetch directory tree.
     * @returns {Promise}
     */
    static fetchDirectoryTree() {
        return axios.get(MediaService.directoryTreeUrl())
            .then(response => {
                return response.data.tree;
            })
            .catch(error => {
                console.error(error);
            });
    }

    /**
     * Create directory.
     * @param {object} directory - parent directory
     * @param {object} data - directory data
     * @returns {Promise}
     */
    static createDirectory(directory, data) {
        return axios.post(MediaService.createDirectoryUrl(directory), data)
            .then(response => {
                return response.data;
            })
            .catch(error => {
                console.error(error);
            });
    }

    /**
     * Update directory.
     * @param {object} directory - directory to update
     * @param {object} data - directory data
     * @returns {Promise}
     */
    static updateDirectory(directory, data) {
        return axios.put(MediaService.updateDirectoryUrl(directory), data)
            .then(response => {
                return response.data;
            })
            .catch(error => {
                console.error(error);
            });
    }

    /**
     * Delete directory with confirmation.
     * @param {object} directory
     * @param {Localization} localization
     * @returns {Promise}
     */
    static deleteDirectoryWithConfirmation(directory, localization) {
        return swal({
            title: localization.trans('directories.confirm_delete.title'),
            text: localization.trans('directories.confirm_delete.text', directory),
            icon: "warning",
            buttons: {
                cancel: {
                    text: localization.trans('directories.confirm_delete.cancel'),
                    visible: true
                },
                confirm: {
                    text: localization.trans('directories.confirm_delete.confirm'),
                    value: true
                }
            },
            dangerMode: true
        })
            .then(isConfirm => {
                if (isConfirm) {
                    return MediaService.deleteDirectory(directory);
                }

                return Promise.reject();
            });
    }

    /**
     * Delete directory.
     * @protected
     * @param {object} directory - directory to delete
     * @returns {Promise}
     */
    static deleteDirectory(directory) {
        return axios.delete(MediaService.deleteDirectoryUrl(directory))
            .then(response => {
                return response.data;
            })
            .catch(error => {
                console.error(error);
            });
    }

    /**
     * Fetch directory content.
     * @param {object} directory - parent directory
     * @param {object} params - parameters such as sorting etc...
     * @returns {Promise}
     */
    static fetchDirectoryContent(directory, params) {
        return axios.get(MediaService.directoryContentUrl(directory), {params: params})
            .catch(error => {
                console.error(error);
            });
    }

    /**
     * Delete files with confirmation.
     * @param {MediaFile[]|MediaFile} files
     * @param {Localization} localization
     * @returns {Promise}
     */
    static deleteFilesWithConfirmation(files, localization) {
        if (!Array.isArray(files)) {
            files = [files];
        }

        return swal({
            title: localization.choice('file.confirm_delete.title', files.length),
            text: localization.choice('file.confirm_delete.text', files.length, {
                count: files.length,
                name: files[0].getNameWithExtension()
            }),
            icon: "warning",
            buttons: {
                cancel: {
                    text: localization.trans('file.confirm_delete.cancel'),
                    visible: true
                },
                confirm: {
                    text: localization.trans('file.confirm_delete.confirm'),
                    value: true
                }
            },
            dangerMode: true
        })
            .then(isConfirm => {
                if (isConfirm) {
                    return MediaService.deleteFiles(files);
                }
                return Promise.reject();
            });
    }

    /**
     * Delete files (after confirmation).
     * @protected
     * @param {MediaFile[]} files
     * @returns {Promise}
     */
    static deleteFiles(files) {
        const ids = files.map(file => file.getId());

        return axios.delete(MediaService.deleteFilesUrl(ids))
            .then(response => {
                files.forEach(file => {
                    EventBus.$emit(EVENTS.FILE_DELETED, file);
                });

                return response;
            })
            .catch(error => {
                console.error(error);
            });
    }

    /**
     * Delete files (after confirmation).
     * @param {object} params
     * @returns {Promise}
     */
    static search(params) {
        return axios.get(MediaService.searchUrl(), {params: params})
            .catch(error => {
                console.error(error);
            });
    }

    /**
     * Update data of the file.
     * @param {MediaFile} file
     * @param {object} data
     * @returns {Promise}
     */
    static updateFile(file, data) {
        return axios.put(MediaService.updateFileUrl(file), data)
            .then(response => {
                file.updateData(response.data.file);
                return response.data.file;
            })
            .catch(error => {
                console.log(error);
            });
    }

    // URLS

    /**
     * Get url address of endpoint for fetching directory tree.
     * @returns {string}
     */
    static directoryTreeUrl() {
        return MediaService.getUrls().directoryTree;
    }

    /**
     * Get url address of endpoint for creating directory.
     * @param {object} directory
     * @returns {string}
     */
    static createDirectoryUrl(directory) {
        let url = MediaService.getUrls().createDirectory;
        if (directory && directory.id) {
            url += `/${directory.id}`;
        }

        return url;
    }

    /**
     * Get url address of endpoint for updating directory.
     * @param {object} directory
     * @returns {string}
     */
    static updateDirectoryUrl(directory) {
        return MediaService.getUrls().updateDirectory.replace('%_i_%', directory.id);
    }

    /**
     * Get url address of endpoint for deleting directory.
     * @param {object} directory
     * @returns {string}
     */
    static deleteDirectoryUrl(directory) {
        return MediaService.getUrls().deleteDirectory.replace('%_i_%', directory.id);
    }

    /**
     * Get url address of endpoint for fetching directory content.
     * @param {object} directory
     * @returns {string}
     */
    static directoryContentUrl(directory) {
        let url = MediaService.getUrls().directoryContent;
        if (directory && directory.id) {
            url += `/${directory.id}`;
        }

        return url;
    }

    /**
     * Get url address of endpoint for uploading new file.
     * @param {object} directory
     * @returns {string}
     */
    static fileUploadUrl(directory) {
        let url = MediaService.getUrls().upload;
        if (directory && directory.id) {
            url += `/${directory.id}`;
        }

        return url;
    }

    /**
     * Get url address of endpoint for deleting specified files.
     * @param {number[]} fileIds
     * @returns {string}
     */
    static deleteFilesUrl(fileIds) {
        return MediaService.getUrls().deleteFiles.replace('%_a_%', JSON.stringify(fileIds));
    }

    /**
     * Get url address of endpoint for searching in files and directories.
     * @returns {string}
     */
    static searchUrl() {
        return MediaService.getUrls().search;
    }

    /**
     * Get url address of endpoint for updating specified file.
     * @param {MediaFile} file
     * @returns {string}
     */
    static updateFileUrl(file) {
        return MediaService.getUrls().updateFile.replace('%_i_%', file.getId());
    }

    /**
     * Get url address of endpoint for overriding specified file.
     * @param {MediaFile} file
     * @returns {string}
     */
    static overrideFileUrl(file) {
        return MediaService.getUrls().overrideFile.replace('%_i_%', file.getId());
    }

    /**
     * Get url addresses of media library endpoints.
     * @returns {object}
     */
    static getUrls() {
        return (window.mediaLibraryUrls || function () {
            return {};
        })();
    }
}
