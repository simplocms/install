@extends('admin.layouts.blank')

@section('content')
    <form method="POST">
        {!! csrf_field() !!}
        <input type="hidden" name="token" value="{{ $token }}">

        @include('vendor.form._errors')

        <div class="panel panel-body login-form">
            <div class="text-center">
                <div class="icon-object border-warning text-warning">
                    &nbsp;<i class="fa fa-lock"></i>&nbsp;
                </div>
                <h5 class="content-group">
                    {{ trans('auth.reset_form.title') }}
                    <small class="display-block">{{ trans('auth.reset_form.subtitle') }}</small>
                </h5>
            </div>

            <div class="form-group has-feedback">
                <input type="email" name="email" value="{{ $email }}"
                       class="form-control"
                       placeholder="{{ trans('auth.reset_form.email_placeholder') }}"
                       required
                />
                <div class="form-control-feedback">
                    <i class="fa fa-at text-muted"></i>
                </div>
            </div>

            <div class="form-group has-feedback">
                <input type="password" id="password"
                       name="password"
                       class="form-control"
                       placeholder="{{ trans('auth.reset_form.password_placeholder') }}"
                       required
                />
                <div class="form-control-feedback">
                    <i class="fa fa-lock text-muted"></i>
                </div>
            </div>

            <div class="form-group has-feedback">
                <input type="password"
                       id="password_confirmation"
                       name="password_confirmation"
                       class="form-control"
                       placeholder="{{ trans('auth.reset_form.password_confirmation_placeholder') }}"
                       required
                />
                <div class="form-control-feedback">
                    <i class="fa fa-lock text-muted"></i>
                </div>
            </div>

            <button type="submit" class="btn bg-blue btn-block">
                {{ trans('auth.reset_form.btn_submit') }} <i class="icon-arrow-right14 position-right"></i>
            </button>
            {!! link_to_route('admin.auth.login', trans('auth.reset_form.btn_login'), [], [
                'class' => "text-center show mt-15"
            ]) !!}
        </div>
    </form>
@endsection
