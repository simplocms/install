window.pingPaused = false;
window.Request = class Request {

    /**
     * Create GET request.
     * @param {string} url
     * @param {object=} data
     * @param {string=} dataType
     * @return {jqXHR}
     */
    static get (url, data, dataType) {
        const options = {
            type: 'GET',
            data: data,
            dataType: dataType || 'json'
        };

        return this.create(url, options);
    }

    /**
     * Create POST request.
     * @param {string} url
     * @param {object=} data
     * @param {string=} dataType
     * @return {jqXHR}
     */
    static post (url, data, dataType) {
        const options = {
            type: 'POST',
            data: data,
            dataType: dataType || 'json'
        };

        return this.create(url, options);
    }

    /**
     * Create DELETE request.
     * @param {string} url
     * @param {object=} data
     * @param {string=} dataType
     * @return {jqXHR}
     */
    static delete (url, data, dataType) {
        const options = {
            type: 'DELETE',
            data: data,
            dataType: dataType || 'json'
        };

        return this.create(url, options);
    }

    /**
     * Create PING request.
     * @param {string} url
     * @param {number} interval
     * @return {jqXHR}
     */
    static ping (url, interval) {
        const options = {
            type: 'GET',
            dataType: 'json',
            error: this.processError
        };

        return setInterval(() => {
            if (!window.pingPaused) {
                this.create(url, options);
            }
        }, interval);
    }

    /**
     * Create new request.
     * @param {string} url
     * @param {object=} options
     * @return {jqXHR}
     */
    static create (url, options) {
        options.error = this.processError;
        return $.ajax(url, options);
    }

    /**
     * Process request error.
     * @param {jqXHR} jqXHR
     * @param {string} textStatus
     * @param {string} errorThrown
     */
    static processError (jqXHR, textStatus, errorThrown) {
        if (jqXHR.status === 401) {
            window.pingPaused = true;
            const $modal = $('#unlock-account-modal').modal({
                backdrop: 'static'
            });

            $modal.find('form').off('submit').on('submit', function (event) {
                event.preventDefault();
                const $target = $(event.currentTarget);

                if (!$target.lock()) {
                    return;
                }

                Request.post(event.currentTarget.href || this.action, $(this).serializeArray())
                    .done(function (response) {
                        $modal.modal('hide');
                    })
                    .always(function () {
                        $target.unlock();
                    });
            });

            $('input[name="_token"]').val(jqXHR.responseJSON.renew_token);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jqXHR.responseJSON.renew_token
                }
            });
        }
        else if (jqXHR.status && jqXHR.status !== 422) {
            alert(jqXHR.status + ': ' + errorThrown + ' (' + textStatus + ')');
        }
    }
};
