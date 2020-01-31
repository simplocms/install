export default class Form {
    /**
     * New Form instance constructor.
     * @param {object} data
     */
    constructor(data) {
        this.data = data;
        this.errors = {};
        this.locked = false;
        this.changed = false;
        this.dataCollectors = [];
        this.initialize();
    }

    /**
     * Initialize form.
     * @private
     */
    initialize() {
        const self = this;

        for (const field in self.data) {
            if (self.data.hasOwnProperty(field)) {
                Object.defineProperty(self, field, {
                    get: function () {
                        return self.data[field];
                    },
                    set: function (new_value) {
                        if (self.data[field] !== new_value) {
                            this.changed = true;
                        }

                        self.data[field] = new_value;
                    }
                });
            }
        }
    }

    resetChangeState() {
        this.changed = false;
    }

    hasChanged() {
        return this.changed;
    }

    // DATA COLLECTING //

    /**
     * Get form data.
     * @returns {object}
     */
    getData() {
        let data = {};

        for (const field in this.data) {
            const value = this.data[field];

            if (value === null || typeof value === 'undefined') {
                data[field] = null;
            } else if (['boolean', 'number', 'string'].indexOf(typeof value) !== -1) {
                data[field] = value;
            } else if (Array.isArray(value)) {
                data[field] = [...value];
            } else if (typeof value === 'object') {
                if (typeof value.getId === 'function') {
                    data[field] = value.getId();
                } else {
                    data[field] = {...value};
                }
            }
        }

        this.dataCollectors.map(callable => {
            data = {...data, ...callable(data)};
        });

        return data;
    }

    /**
     * Add data collector function.
     * @param {function} callable
     * @return {Form}
     */
    addDataCollector(callable) {
        this.dataCollectors.push(callable);
        return this;
    }

    // LOCK //

    /**
     * Is form locked?
     * @returns {boolean}
     */
    isLocked() {
        return this.locked;
    }

    /**
     * Lock the form if not locked already.
     * Returns false when form is already locked, otherwise returns true.
     * @returns {boolean}
     */
    lock() {
        if (this.locked) {
            return false;
        }

        return this.locked = true;
    }

    /**
     * Unlock the form.
     */
    unlock() {
        this.locked = false;
    }

    // COMMUNICATION //

    /**
     * POST data to the given url.
     * @param {string} url
     * @returns {Promise}
     */
    post(url) {
        return this.send(axios.post, url);
    }

    /**
     * PUT data to the given url.
     * @param {string} url
     * @returns {Promise}
     */
    put(url) {
        return this.send(axios.put, url);
    }

    /**
     * PATCH data to the given url.
     * @param {string} url
     * @returns {Promise}
     */
    patch(url) {
        return this.send(axios.patch, url);
    }

    /**
     * Send data to the given url.
     * @private
     * @param {function} callback
     * @param {string} url
     * @returns {Promise}
     */
    send(callback, url) {
        this.clearErrors();

        return callback(url, this.getData())
            .then(response => {
                this.unlock();

                if (response.data.redirect) {
                    window.location = response.data.redirect;
                }

                return response;
            })
            .catch(thrown => {
                this.unlock();

                if (thrown.response.status === 422) {
                    this.setErrors(thrown.response.data.errors);
                }

                throw thrown;
            });
    }

    // ERRORS //

    /**
     * Check if specified field has error.
     * @param {string} field
     * @returns {boolean}
     */
    hasError(field) {
        return this.errors.hasOwnProperty(field);
    }

    /**
     * Get error on specified field.
     * @param {string} field
     * @returns {string|null}
     */
    getError(field) {
        if (this.hasError(field)) {
            return this.errors[field];
        }
    }

    /**
     * Set errors to the form.
     * @param {object} errors
     */
    setErrors(errors) {
        this.errors = {};

        for (const field in errors) {
            if (errors.hasOwnProperty(field)) {
                this.errors[field] = errors[field].join(' ');
            }
        }
    }

    /**
     * Clear errors on the form.
     */
    clearErrors() {
        this.errors = {};
    }

    /**
     * Clear error on the specified field.
     * @param {string} field
     */
    clearError(field) {
        delete this.errors[field];
    }
}


