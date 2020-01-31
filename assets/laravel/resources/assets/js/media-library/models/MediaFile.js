export default class MediaFile {
    constructor(data) {
        if (typeof data.updateData === 'function') {
            this.data = {...data.data};
        } else {
            this.data = data;
        }
    }

    updateData(data) {
        this.data = data;
    }

    getCreatedAt() {
        const date = new Date(this.data.created_at * 1000);
        return date.toLocaleString('cs-CZ');
    }

    getUpdatedAt() {
        const date = new Date(this.data.updated_at * 1000);
        return date.toLocaleString('cs-CZ');
    }

    getHumanSize() {
        const i = this.data.size === 0 ? 0 : Math.floor(Math.log(this.data.size) / Math.log(1024));
        return (this.data.size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
    }

    getResolutionText() {
        return this.data.image_resolution + 'px';
    }

    isResolutionAvailable() {
        return !!this.data.image_resolution;
    }

    getPreview() {
        return new LinkBuilder(this);
    }

    getExtension() {
        return this.data.extension;
    }

    getDescription() {
        return this.data.description;
    }

    getName() {
        return this.data.name;
    }

    getNameWithExtension() {
        return `${this.data.name}.${this.data.extension}`;
    }

    getUrl() {
        return this.data.url;
    }

    getId() {
        return this.data.id;
    }

    isImage() {
        return this.data.selectable_image;
    }

    hasPreview() {
        return this.data.supported_image;
    }

    getLastmod() {
        return this.data.updated_at;
    }

    getMimeType() {
        return this.data.mime_type;
    }

    getWidth() {
        if (typeof this.data.width !== 'undefined') {
            return this.data.width;
        }

        return this.data.width = Number(this.data.image_resolution.split('x')[0] || 0);
    }

    getHeight() {
        if (typeof this.data.height !== 'undefined') {
            return this.data.height;
        }

        return this.data.height = Number(this.data.image_resolution.split('x')[1] || 0);
    }
}

const IMAGE_PREVIEW_OPERATIONS = {
    FIT_TO_CANVAS: 'cfit',
    RESIZE: 'size',
    FIT: 'fit',
    CROP: 'crop',
    GREYSCALE: 'bw',
    FORMAT: 'format',
};

class LinkBuilder {
    constructor(file) {
        this.file = file;
        this.operations = [];
    }

    fitToCanvas(width, height, color = null) {
        let operation = `${IMAGE_PREVIEW_OPERATIONS.FIT_TO_CANVAS}=${width}x${height}`;
        if (color) {
            operation += `x${color}`;
        }

        this.operations.push(operation);
        return this;
    }

    resize(width, height) {
        this.operations.push(`${IMAGE_PREVIEW_OPERATIONS.RESIZE}=${width}x${height}`);
        return this;
    }

    fit(width, height) {
        this.operations.push(`${IMAGE_PREVIEW_OPERATIONS.FIT}=${width}x${height}`);
        return this;
    }

    crop(width, height) {
        this.operations.push(`${IMAGE_PREVIEW_OPERATIONS.CROP}=${width}x${height}`);
        return this;
    }

    greyscale() {
        this.operations.push(IMAGE_PREVIEW_OPERATIONS.GREYSCALE);
        return this;
    }

    preview() {
        this.operations.push(`${IMAGE_PREVIEW_OPERATIONS.FORMAT}=png,svg,jpeg,gif,webp,bmp,ico`);
        return this;
    }

    getUrl() {
        let url = this.file.getUrl();

        if (!this.operations.length) {
            return url;
        }

        // Add lastmod to update the cache when image changes
        this.operations.push(`lastmod=${this.file.getLastmod()}`);

        const query = this.operations.join('&');
        return url + '?' + query;
    }
}
