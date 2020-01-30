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
    })
});
</script>