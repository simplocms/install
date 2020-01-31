{{ Form::model($configuration, ['id' => 'gem-link-configuration-form']) }}
    <div class="form-group">
        {{ Form::label($id = 'ml-text-input', trans('module-link::admin.grid_editor_form.labels.text')) }}
        {{ Form::text($name = 'text', null, [
            'class' => 'form-control',
            'id' => $id
        ]) }}
        @include('vendor.form.field_error')
    </div>

    <div class="form-group">
        {{ Form::checkbox($name = 'custom_url', 1, $customUrl = !!$configuration->url, [
            'id' => $id = 'ml-custom-url-input',
            'v-model' => 'customUrl'
        ]) }}
        {{ Form::label($id, trans('module-link::admin.grid_editor_form.labels.custom_url')) }}
        @include('vendor.form.field_error')
    </div>

    <div v-show="!customUrl">
        <div class="form-group">
            {{ Form::label($id = 'ml-model-input', trans('module-link::admin.grid_editor_form.labels.model_type')) }}
            {{ Form::select($name = 'model_type', $models, null, [
                'class' => 'form-control',
                'id' => $id,
                'v-model' => 'model'
            ]) }}
            @include('vendor.form.field_error')
        </div>

        @foreach($modelLists as $key => $list)
        <div class="form-group" v-show="model == '{{ $key }}'">
            {{ Form::label($id = 'ml-' . $key . '-id-input', $models[$key]) }}
            {{ Form::select( $name = $key . '_id', $list, null, [
                'class' => 'form-control',
                'id' => $id
            ]) }}
            @include('vendor.form.field_error')
        </div>
        @endforeach
    </div>

    <div class="form-group" v-show="customUrl">
        {{ Form::label($id = 'ml-url-input', trans('module-link::admin.grid_editor_form.labels.custom_url')) }}
        {{ Form::text($name = 'url', null, [
            'class' => 'form-control',
            'id' => $id
        ]) }}
        @include('vendor.form.field_error')
    </div>

    <div class="form-group">
        {{ Form::label($id = 'ml-view-input', trans('module-link::admin.grid_editor_form.labels.view')) }}
        {{ Form::select($name = 'view', $views, null, [
            'class' => 'form-control',
            'id' => $id
        ]) }}
        @include('vendor.form.field_error')
    </div>

    <div class="form-group">
        {{ Form::label('ml-attributes-input', trans('module-link::admin.grid_editor_form.labels.attributes')) }}

        <script type="text/x-template" id="attribute-row">
            <div class="row">
                <div class="col-xs-11">
                    <div class="input-group" style="margin-bottom: 10px">
                        {{ Form::text('attribute_key[]', null, [
                            'class' => 'form-control',
                            'v-bind:value' => 'attribute.name'
                        ]) }}
                        <div class="input-group-addon"> = </div>
                        {{ Form::text('attribute_value[]', null, [
                            'class' => 'form-control',
                            'v-bind:value' => 'attribute.value'
                        ]) }}
                    </div>
                </div>
                <div class="col-xs-1">
                    <button @click.prevent="$emit('remove')"
                            type="button"
                            class="btn btn-default"
                    >
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        </script>

        <link-attribute v-for="(attribute, index) in attributes"
                        @remove="removeAttribute(index)"
                        :attribute='attribute'
                        :key="index"
        >
        </link-attribute>

        <div class="text-center">
            <a @click.prevent="addAttributeRow" href="#">
                {{ trans('module-link::admin.grid_editor_form.btn_add_attribute') }}
            </a>
        </div>
    </div>
{{ Form::close() }}

<script>
    window.gemLinkConfigurationFormOptions = function () {
        return {
            attributes: {!! json_encode($configuration->javascript_tags) !!}
        }
    };
</script>
{!! Html::script(mix('plugin/js/switchery.js')) !!}
{!! Html::script($module->mix('config.js')) !!}
