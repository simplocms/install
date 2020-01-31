{{ Form::model($configuration, ['id' => 'model-view-configuration-form']) }}

<div class="alert alert-warning" v-if="!hasViews">
    {{ trans('module-view::admin.grid_editor_form.no_views') }}
</div>

<div v-else>
    <div class="form-group {{ $errors->has($name = 'view') ? 'has-error' : '' }}">
        {{ Form::label($id = 'mv-view-input', trans('module-view::admin.grid_editor_form.label_view')) }}
        {{ Form::select('view', $views, null, [
            'class' => 'form-control',
            'id' => $id,
            'v-model' => 'formData.view',
            '@change' => 'loadVariables()'
        ]) }}
        @include('admin.vendor.form.field_error')
    </div>

    <variable-field v-for="(field, index) in fields"
                    :key="index"
                    :field="field"
                    v-model="formData.variables[field.name]"
                    ref="fields"
    ></variable-field>
</div>

{{ Form::close() }}

<script>
    window.viewModuleOptions = function () {
        return {!! json_encode([
            'model' => $configuration->getFormAttributes(['view']),
            'variables' => isset($variables) ? $variables : null,
            'variablesUri' => route('module.view.variables'),
            'views' => $views,
            'CKEditorUri' => url("plugin/js/ckeditor.js")
        ]) !!};
    };
</script>
{{ Html::script($module->mix('configuration.js')) }}
<style>
    :root {
        --ck-z-default: 100
    }
</style>
