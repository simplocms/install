{{ Form::model($configuration, [
    'id' => 'mt-configuration-form'
]) }}

    <div class="form-group">
        {{ Form::label($id = 'mt-content-input', trans('module-text::admin.grid_editor_form.label_content')) }}
        {{ Form::textarea('content', null, [
            'class' => 'form-control',
            'id' => $id
        ]) }}
    </div>

{{ Form::close() }}

{!! Html::script(url("plugin/js/ckeditor.js")) !!}
@if (app()->getLocale() !== 'en')
    {!! Html::script(url("js/localizations/ckeditor/" . app()->getLocale() . ".js")) !!}
@endif
<script>
    (function ($) {
        var editor = null;

        ClassicEditor.create(document.querySelector('#mt-content-input'))
            .then(function (instance) {
                editor = instance;
            });

        $('#mt-configuration-form').on('admin:before-form-submit', function () {
            $('#mt-content-input').val(editor.getData());
        });

        $('#module-form-modal').on('hide.bs.modal', function () {
            editor.destroy();
            delete window.CKEDITOR_VERSION;
        });
    })(jQuery);
</script>

<style>
:root {
   --ck-z-default: 100
}
</style>
