<?php
/** @var \Modules\Image\Models\Configuration $configuration */
/** @var \App\Models\Module\Module $module */
?>
{{ Form::model($configuration, [
    'id' => 'm-image-configuration-form'
]) }}

{{-- Image --}}
<div class="form-group {{ $errors->has($name = 'image_id') ? 'has-error' : '' }}">
    <media-library-file-selector :image="true"
                                 input-name="image_id"
                                 error="{{ $errors->first('image_id') }}"
                                 v-model="formData.image_id"
                                 @change="image = $event"
    ></media-library-file-selector>
</div>

{{-- Size --}}
<div class="form-group {{ $errors->has($name = 'is_sized') ? 'has-error' : '' }}">
    {!! Form::label($name, trans('module-image::admin.grid_editor_form.labels.is_sized')) !!}

    {{ Form::select($name, [
        0 => trans('module-image::admin.grid_editor_form.size_options.automatic'),
        1 => trans('module-image::admin.grid_editor_form.size_options.manual')
    ], null, [
        'class' => 'form-control',
        'v-model' => 'formData.is_sized'
    ]) }}

    @include('admin.vendor.form.field_error')
</div>

<!-- Resolution -->
<div class="form-group {{ $errors->has('width') || $errors->has('height') ? 'has-error' : '' }}"
     v-if="formData.is_sized != 0"
>
    {!! Form::label('width', trans('module-image::admin.grid_editor_form.labels.resolution')) !!}

    <div class="input-group">
        {{ Form::number('width', null, [
            'class' => 'form-control',
            'v-model' => 'formData.width',
            'min' => 1,
            '@change' => 'widthChanged',
            ':disabled' => '!image'
        ]) }}
        <div class="input-group-addon">x</div>
        {{ Form::number('height', null, [
            'class' => 'form-control',
            'v-model' => 'formData.height',
            'min' => 1,
            '@change' => 'heightChanged',
            ':disabled' => '!image'
        ]) }}
        <div class="input-group-addon">px</div>
    </div>

    @include('admin.vendor.form.field_error', ['name' => 'width'])
    @include('admin.vendor.form.field_error', ['name' => 'height'])
</div>

{{-- Alt --}}
<div class="form-group required {{ $errors->has($name = 'alt') ? 'has-error' : '' }}">
    {!! Form::label($name, trans('module-image::admin.grid_editor_form.labels.alt')) !!}

    {{ Form::text($name, null, [
        'class' => 'form-control',
        'v-model' => 'formData.alt'
    ]) }}

    @include('admin.vendor.form.field_error')
</div>

{{-- Class --}}
<div class="form-group {{ $errors->has($name = 'img_class') ? 'has-error' : '' }}">
    {!! Form::label($name, trans('module-image::admin.grid_editor_form.labels.img_class')) !!}

    {{ Form::text($name, null, [
        'class' => 'form-control',
        'v-model' => 'formData.img_class'
    ]) }}

    @include('admin.vendor.form.field_error')
</div>

{{ Form::close() }}

<script>
    window.imageModuleOptions = function () {
        return {!! json_encode([
            'model' => $configuration->getFormAttributes(['is_sized', 'width', 'height', 'alt', 'img_class', 'image_id'])
        ]) !!};
    };
</script>
{{ Html::script($module->mix('configuration.js')) }}
