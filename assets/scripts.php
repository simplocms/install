<script>
$(document).ready(function() {
    var dbFormData = {};
    
    $('#dbButton').on('click', function($e) {
        $e.preventDefault();

        var form = $('#dbForm');
        var dbResult = $('#dbResult');
        dbFormData = {};

        $.each(form.serializeArray(), function() {
            dbFormData[this.name] = this.value;
        });

        sendRequest(dbFormData, true)
        .done(function(data) {
            dbResult.text('Connection successful');
        }).fail(function(data) {
            dbResult.text(data.responseText);
        })
    });

    $('#admin_password').on('input', function() {
        let value = $(this).val();
        let errEl = $('#adminPasswordError');
        let html = '';

        if (! value) {
            errEl.html(html);
            return;
        }

        if (value.toLowerCase() == value) {
            html += 'Need at least one upper case letter<br>';
        }

        if (value.toUpperCase() == value) {
            html += 'Need at least one lower case letter<br>';
        }

        errEl.html(html);
    });

    $('#adminButton').on('click', function(e) {
        e.preventDefault();

        var i = 0;
        var form = $('#dbForm');
        var dbResult = $('#dbResult');
        var formData = {};

        $.each(form.serializeArray(), function() {
            formData[this.name] = this.value;
        });

        unzipData = formData;
        unzipData['unzip_file'] = true;

        sendRequest(unzipData, false)
        .done(function(data) {
            console.log(data)
        }).fail(function(data) {
            console.log(data)
        })

        sendRequest({composer: true}, false)
        .done(function(data) {
            console.log(data)
        }).fail(function(data) {
            console.log(data)
        })

        sendRequest({migrate: true}, false)
        .done(function(data) {
            console.log(data);
            sendRequest({db_seed: true}, false)
            .done(function(data) {
                console.log(data)
            }).fail(function(data) {
                console.log(data)
            })
        }).fail(function(data) {
            console.log(data)
        })

        sendRequest({install_npm: true}, false, function() {
            console.log("installing npm");
        })
        .done(function(data) {
            console.log(data)
        }).fail(function(data) {
            console.log(data)
        })
    })
});

function sendRequest(sendData, sendAsync, sendBeforeSend = null)
{
    return $.ajax({
        url: window.location.pathname,
        method: 'POST',
        data: sendData,
        async: sendAsync,
        beforeSend: sendBeforeSend
    });
}
</script>