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
            url: form.attr('action'),
            method: form.attr('method'),
            data: formData
        }).done(function(data) {
            dbResult.text(data);
        }).fail(function(data) {
            dbResult.text(data);
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

    $('#admin_password_confirm').on('input', function() {
        let value = $(this).val();
        let errEl = $('#adminPasswordConfirmError');

        if (value !== $('#admin_password').val()) {
            errEl.html('Passwords must be same');
        } else {
            errEl.html('');
        }
    });

    $('#adminButton').on('click', function(e) {
        e.preventDefault();
    })
});
</script>