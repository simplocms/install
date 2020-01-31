<div class="tabbable tab-content-bordered">
    <ul class="nav nav-tabs nav-tabs-highlight">
        <li class="active">
            <a href="#tab_details" data-toggle="tab" aria-expanded="true">
                {{ trans('admin/languages/form.tabs.details') }}
            </a>
        </li>
    </ul>
    <div class="tab-content">

        <div class="tab-pane has-padding active" id="tab_details">
            <div class="form-group required {{ $errors->has($name = 'name') ? 'has-error' : '' }}">
                {!! Form::label($name, trans("admin/languages/form.labels.$name")) !!}
                {!! Form::text($name, null, [
                    'class' => 'form-control maxlength',
                    'maxlength' => 50
                ]) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group required {{ $errors->has($name = 'country_code') ? 'has-error' : '' }}">
                {!! Form::label($name, trans("admin/languages/form.labels.$name")) !!}
                {!! Form::text($name, null, [
                    'class' => 'form-control maxlength',
                    'maxlength' => 3
                ]) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group required {{ $errors->has($name = 'language_code') ? 'has-error' : '' }}">
                {!! Form::label($name, trans("admin/languages/form.labels.$name")) !!}
                {!! Form::text($name, isset($language)?$language->locale:null, [
                    'class' => 'form-control maxlength',
                    'maxlength' => 3
                ]) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group {{ $errors->has($name = 'domain') ? 'has-error' : '' }}">
                {!! Form::label($name, trans("admin/languages/form.labels.$name")) !!}
                {!! Form::text($name, $language->domain ?? null, [
                    'class' => 'form-control maxlength',
                    'maxlength' => 255,
                    'placeholder' => trans("admin/languages/form.placeholders.$name")
                ]) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="checkbox checkbox-switchery">
                <label>
                    {!! Form::checkbox('enabled', 1, null, ['id' => 'input-enabled']) !!}
                    {{ trans("admin/languages/form.labels.enabled") }}
                </label>
            </div>
        </div>
    </div>
</div>
