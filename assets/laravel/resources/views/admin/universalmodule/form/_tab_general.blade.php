<field v-for="(field, index) in fields"
       :key="field.name"
       :field="field"
       v-model="form.fields[field.name]"
       ref="fields"
></field>

@if ($module->hasUrl())
    <v-form-group :error="form.getError('url')" :required="true">
        {!! Form::label('url', trans('admin/universal_modules.form.label_url')) !!}
        {!! Form::text('url', $moduleData->url, [
            'class' => 'form-control',
            'v-model' => 'form.url',
            'maxlength' => '200',
            'v-maxlength',
        ]) !!}
    </v-form-group>
@endif

@if ($module->isAllowedOrdering())
    <v-form-group :error="form.getError('order')">
        {!! Form::label('order', trans('admin/universal_modules.form.label_order')) !!}
        {!! Form::number('order', $moduleData->order, [
            'class' => 'form-control',
            'v-model.number' => 'form.order',
            'min' => 1
        ]) !!}
    </v-form-group>
@endif

@if ($module->isAllowedToggling())
    <v-form-group :error="form.getError('enabled')">
        <v-checkbox-switch v-model="form.enabled" name="enabled">
            {{ trans('admin/universal_modules.form.label_enabled') }}
        </v-checkbox-switch>
    </v-form-group>
@endif
