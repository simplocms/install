const STATUS = {
    INITIALIZED: 1,
    DONE: 2,
    FAILED: 3,
    UPLOADING: 4,
    CANCELED: 5,
};

export default class Uploader {
    /**
     *
     * @param {string} url
     * @param {File} file
     */
    constructor(url, file) {
        this.url = url;
        this.file = file;

        this.status = STATUS.INITIALIZED;
        this.chunkSize = 204800; // 200kB (1024 * 200)
        this.chunkOffset = 0;
        this.uuid = this.createGUID();
        this.progressCallbacks = [];
        this.finishCallbacks = [];
        this.failCallbacks = [];
        this.cancelCallbacks = [];
        this.resultFile = null;
        this.cancelToken = null;
    }

    start() {
        this.status = STATUS.UPLOADING;
        this.uploadNextChunk();
        return this;
    }

    async getNextBlob() {
        return new Promise((resolve, abort) => {
            const start = this.chunkOffset || 0;
            const stop = this.chunkOffset + this.chunkSize;

            const reader = new FileReader();

            reader.onload = event => {
                resolve(new Blob([new Uint8Array(event.target.result)], {type: this.file.type}));
            };

            reader.onerror = event => {
                abort(event);
            };

            const blob = this.file.slice(start, stop);
            reader.readAsArrayBuffer(blob);
        });
    }

    async uploadNextChunk() {
        const args = {};
        let currentBlob = await this.getNextBlob();

        args.name = this.getFilename();
        args.uuid = this.uuid;

        args.chunk = Math.ceil(this.chunkOffset / this.chunkSize);
        args.chunks = Math.ceil(this.file.size / this.chunkSize);

        if (this.chunkOffset === 0) {
            args.isFirst = true;
        }

        if ((this.chunkOffset + this.chunkSize) >= this.file.size) {
            args.isLast = true;
        }

        return this.uploadChunk(currentBlob, args);
    }

    uploadChunk(chunkBlob, args) {
        this.cancelToken = axios.CancelToken.source();
        const config = {
            onUploadProgress: progressEvent => {
                const percentCompleted = Math.round(((this.chunkOffset + progressEvent.loaded) * 100) / this.file.size);
                this.progressCallbacks.forEach(callback => callback(Math.min(percentCompleted, 100)));
            },
            cancelToken: this.cancelToken.token
        };

        const data = new FormData();

        for (const arg in args) {
            data.append(arg, args[arg]);
        }

        data.append('file', chunkBlob);

        return axios.post(this.url, data, config)
            .then(response => {
                if (!args.isLast) {
                    this.chunkOffset += this.chunkSize;
                    this.uploadNextChunk();
                } else {
                    this.resultFile = response.data.file;
                    this.status = STATUS.DONE;
                    this.finishCallbacks.forEach(callback => callback(this.resultFile));
                }
            })
            .catch(error => {
                if (axios.isCancel(error)) {
                    this.cleanUploadAfterCanceled();
                    return;
                }

                console.error(error);
                this.status = STATUS.FAILED;
                this.failCallbacks.forEach(callback => callback(error));
            });
    }

    cleanUploadAfterCanceled() {
        const params = {
            uuid: this.uuid,
            name: this.getFilename()
        };

        return axios.delete(this.url, {params: params})
            .then(response => {
                this.cancelCallbacks.forEach(callback => callback());
            })
    }

    retry() {
        this.status = STATUS.UPLOADING;
        this.uploadNextChunk();
    }

    cancel() {
        this.status = STATUS.CANCELED;

        if (this.cancelToken !== null) {
            this.cancelToken.cancel();
        } else {
            this.cancelCallbacks.forEach(callback => callback());
        }
    }

    getFilename() {
        return this.file.name || this.file.target_name;
    }

    getFileSize() {
        const i = this.file.size === 0 ? 0 : Math.floor(Math.log(this.file.size) / Math.log(1024));
        return (this.file.size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
    }

    onProgress(callback) {
        this.progressCallbacks.push(callback);
        return this;
    }

    onFinish(callback) {
        if (this.status === STATUS.DONE) {
            callback(this.resultFile);
        } else {
            this.finishCallbacks.push(callback);
        }

        return this;
    }

    onFail(callback) {
        if (this.status === STATUS.FAILED) {
            callback();
        }

        this.failCallbacks.push(callback);
        return this;
    }

    onCancel(callback) {
        if (this.status === STATUS.CANCELED) {
            callback();
        }

        this.cancelCallbacks.push(callback);
        return this;
    }

    /**
     * Generate Global unique string.
     * @returns {string}
     */
    createGUID() {
        let words = [];
        for (let i = 0; i <= 7; i++) {
            words.push((((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1));
        }

        const uuid = words[0] + words[1] + '-' + words[2] + '-4' + words[3].substr(0, 3) + '-' + words[4] + '-' + words[5] + words[6] + words[7];

        return uuid.toLowerCase();
    }
}
