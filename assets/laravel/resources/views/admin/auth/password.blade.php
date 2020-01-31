@extends('admin.layouts.blank')

@section('content')
    {{ Form::open([ 'route' => 'admin.password.forgot' ]) }}
        <div class="panel panel-body login-form">
            <div class="text-center">
                <div class="icon-object border-warning text-warning">
                    <i class="fa fa-unlock"></i>
                </div>
                <h5 class="content-group">
                    {{ trans('auth.password_form.title') }}
                    <small class="display-block">{{ trans('auth.password_form.subtitle') }}</small>
                </h5>
            </div>

            @include('vendor.form._errors')

            <div class="form-group has-feedback">
                {{ Form::email('email', null, [
                    'class' => 'form-control',
                    'required', 'autofocus',
                    'placeholder' => trans('auth.password_form.email_placeholder')
                ]) }}
                <div class="form-control-feedback">
                    <i class="fa fa-at text-muted"></i>
                </div>
            </div>

            <button type="submit" class="btn bg-blue btn-block">
                {{ trans('auth.password_form.btn_submit') }}
            </button>
            {!! link_to_route('admin.auth.login', trans('auth.password_form.btn_login'), [], [
                'class' => "text-center show mt-15"
            ]) !!}
        </div>
    {{ Form::close() }}
@endsection
