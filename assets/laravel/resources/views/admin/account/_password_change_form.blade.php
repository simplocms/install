{{ Form::model($user, ['id' => 'form-password-change', 'route' => 'admin.account.password.change']) }}

<div class="form-group {{ $errors->has($name = 'password') ? 'has-error' : '' }}">
    {!! Form::label($name, trans("admin/account/form.password.labels.$name")) !!}
    {!! Form::password($name, [
        'class' => 'form-control '
    ]) !!}
    @include('admin.vendor.form.field_error')
</div>

<div class="form-group {{ $errors->has($name = 'new_password') ? 'has-error' : '' }}">
    {!! Form::label($name, trans("admin/account/form.password.labels.$name")) !!}
    {!! Form::password($name, [
        'class' => 'form-control '
    ]) !!}
    @include('admin.vendor.form.field_error')
</div>

<div class="form-group {{ $errors->has($name = 'verify_new_password') ? 'has-error' : '' }}">
    {!! Form::label($name, trans("admin/account/form.password.labels.$name")) !!}
    {!! Form::password($name, [
        'class' => 'form-control '
    ]) !!}
    @include('admin.vendor.form.field_error')
</div>

<div class="text-right">
    <button type="submit" class="btn btn-primary">
        {{ trans("admin/account/form.password.button_submit") }}
    </button>
</div>
{{ Form::close() }}
