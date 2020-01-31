{{ Form::model($user, ['id' => 'form-account-edit', 'files' => true]) }}

    <div class="form-group {{ $errors->has($name = 'firstname') ? 'has-error' : '' }}">
        {!! Form::label($name, trans("admin/account/form.general.labels.$name")) !!}
        {!! Form::text($name, null, [
            'class' => 'form-control maxlength',
            'maxlength' => '250'
        ]) !!}
        @include('admin.vendor.form.field_error')
    </div>

    <div class="form-group {{ $errors->has($name = 'lastname') ? 'has-error' : '' }}">
        {!! Form::label($name, trans("admin/account/form.general.labels.$name")) !!}
        {!! Form::text($name, null, [
            'class' => 'form-control maxlength',
            'maxlength' => '250'
        ]) !!}
        @include('admin.vendor.form.field_error')
    </div>

    <div class="form-group {{ $errors->has($name = 'email') ? 'has-error' : '' }}">
        {!! Form::label($name,trans("admin/account/form.general.labels.$name")) !!}
        {!! Form::email($name, null, [
            'class' => 'form-control maxlength',
            'maxlength' => '255',
            'required'
        ]) !!}
        @include('admin.vendor.form.field_error')
    </div>

    <div class="form-group {{ $errors->has($name = 'username') ? 'has-error' : '' }}">
        {!! Form::label($name, trans("admin/account/form.general.labels.$name")) !!}
        {!! Form::text($name, null, [
            'class' => 'form-control maxlength',
            'maxlength' => '100'
        ]) !!}
        @include('admin.vendor.form.field_error')
    </div>

    <v-form-group>
        <label for="input-twitter-account">{{ trans("admin/account/form.general.labels.twitter_account") }}</label>
        <span class="help-block pb-10">{!! trans('admin/general.twitter.help_text') !!}</span>
        {!! Form::text('twitter_account', null, [
            'class' => 'form-control',
            'maxlength' => '50',
            'placeholder' => '@',
            'id' => 'input-twitter-account',
            'v-maxlength'
        ]) !!}
        @include('admin.vendor.form.field_error')
    </v-form-group>

    <div class="form-group {{ $errors->has($name = 'position') ? 'has-error' : '' }}">
        {!! Form::label($name, trans("admin/account/form.general.labels.$name")) !!}
        {!! Form::text($name, null, [
            'class' => 'form-control maxlength',
            'maxlength' => '250'
        ]) !!}
        @include('admin.vendor.form.field_error')
    </div>

    <div class="form-group {{ $errors->has($name = 'locale') ? 'has-error' : '' }}">
        {!! Form::label($name, trans("admin/account/form.general.labels.$name")) !!}
        {!! Form::select($name, $locales, null, [
            'class' => 'form-control'
        ]) !!}
        @include('admin.vendor.form.field_error')
    </div>

    <div class="form-group {{ $errors->has($name = 'about') ? 'has-error' : '' }}">
        {!! Form::label($name, trans("admin/account/form.general.labels.$name")) !!}
        {!! Form::textarea($name, null, [
            'class' => 'form-control maxlength',
            'maxlength' => '1000'
        ]) !!}
        @include('admin.vendor.form.field_error')
</div>

    {{-- Image --}}
    <div class="clearfix image-input-control">
        <div class="thumbnail display-inline-block pull-left">
            <img :src="imageSrc" alt="Náhled obrázku" v-show="imageSrc">
        </div>
        <div class="col-xs-6">
            <button class="btn btn-primary" type="button" @click="openFileInput">
                <span v-if="!imageSelected">
                    {{ trans("admin/account/form.general.buttons.choose_image") }}
                </span>
                <span v-if="imageSelected">
                    {{ trans("admin/account/form.general.buttons.change_image") }}
                </span>
            </button>
            <button class="btn btn-default" type="button" v-show="imageSelected" @click="removeImage">
                {{ trans("admin/account/form.general.buttons.remove_image") }}
            </button>
            @include('admin.vendor.form.field_error', ['name' => 'image'])
            {{ Form::file('image', [
                'id' => 'image-input',
                'class' => 'hidden',
                'accept' => "image/*",
                '@change' => 'previewThumbnail'
            ]) }}
            {{ Form::hidden('remove_image', null, [
                ':value' => 'removeImageValue'
            ]) }}
        </div>
    </div>

    <div class="text-right">
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i> {{trans('admin/account/form.general.buttons.submit')}}
        </button>
    </div>

{{ Form::close() }}
