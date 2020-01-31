import {FILE_TYPE, COMMANDS} from './enums';

export default class MediaLibraryPrompt {
    constructor() {
        this.multiSelect = false;
        this.type = FILE_TYPE.ANY;
        this.select = null;
        this.selectCallback = null;
        this.unselectCallback = null;
    }

    static singleImage() {
        return new MediaLibraryPrompt().fileType(FILE_TYPE.IMAGE);
    }

    static singleVideo() {
        return new MediaLibraryPrompt().fileType(FILE_TYPE.VIDEO);
    }

    static multiImage() {
        return new MediaLibraryPrompt().fileType(FILE_TYPE.IMAGE).multiple();
    }

    static singleFile() {
        return new MediaLibraryPrompt();
    }

    static multiFile() {
        return new MediaLibraryPrompt().multiple();
    }

    open() {
        const params = {
            type: this.type,
            multi: this.multiSelect,
            onSelect: this.selectCallback,
            onUnselect: this.unselectCallback,
        };

        if (this.multiSelect && this.select) {
            params.files = this.select;
        } else if (this.select) {
            params.file = this.select;
        }

        return new Promise((resolve, reject) => {
            window.EventBus.$emit(COMMANDS.OPEN_PROMPT, {...params, ok: resolve});
        });
    }

    fileType(type) {
        this.type = type;
        return this;
    }

    multiple(multiSelect = true) {
        this.multiSelect = multiSelect;
        return this;
    }

    selectFiles(files) {
        this.select = files;
        return this;
    }

    onSelect(callback) {
        this.selectCallback = callback;
        return this;
    }

    onUnselect(callback) {
        this.unselectCallback = callback;
        return this;
    }
}
