<script>
$(document).ready(function() {
    $('#dbButton').on('click', function($e) {
        $e.preventDefault();

        let form = $('#dbForm');
        let dbResult = $('#dbResult');
        let formData = {};

        $.each(form.serializeArray(), function() {
            formData[this.name] = this.value;
        });

        $.ajax({
            url: window.location.pathname,
            method: 'POST',
            data: formData,
        }).done(function(data) {
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

        let i = 0;
        let form = $('#dbForm');
        let dbResult = $('#dbResult');
        let formData = {};

        $.each(form.serializeArray(), function() {
            formData[this.name] = this.value;
        });

        $.ajax({
            url: window.location.pathname,
            method: 'POST',
            data: {
                install_app: true
            }
        }).done(function(data) {
            console.log('file downloaded')
        }).fail(function(file) {
            console.log('error')
        })

        unzipData = formData;
        unzipData['unzip_file'] = true;

        $.ajax({
            url: window.location.pathname,
            method: 'POST',
            data: unzipData
        }).progress(function() {
            console.log(progress + i)
            i += 1;
        }).done(function(data) {
            console.log(data)
        }).fail(function(data) {
            console.log(data)
        })
    })
});
</script>