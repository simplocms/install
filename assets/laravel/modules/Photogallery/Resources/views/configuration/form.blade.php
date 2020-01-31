{{ Form::model($configuration) }}

<div class="form-group {{ $errors->has($name = 'photogallery_id') ? 'has-error' : '' }}">
    {{ Form::label($id = 'mp-photogallery-id-input', trans('module-photogallery::admin.grid_editor_form.labels.photogallery')) }}
    {{ Form::select('photogallery_id', $photogalleries, null, [
        'class' => 'form-control',
        'id' => $id
    ]) }}
    @include('admin.vendor.form.field_error')
</div>


<div class="form-group {{ $errors->has($name = 'view') ? 'has-error' : '' }}">
    {{ Form::label($id = 'mp-view-input', trans('module-photogallery::admin.grid_editor_form.labels.view')) }}
    {{ Form::select('view', $views, null, [
        'class' => 'form-control',
        'id' => $id
    ]) }}
    @include('admin.vendor.form.field_error')
</div>

{{ Form::close() }}
