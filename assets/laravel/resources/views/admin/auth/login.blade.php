@extends('admin.layouts.blank')

@section('content')
    <page-login inline-template>
        {{ Form::open([ 'id' => 'login-form' ]) }}
            @if (isset($redirect_to))
                <input type="hidden" name="_redirect" value="{{ $redirect_to }}"/>
            @endif

            <div class="panel panel-body login-form">
                <div class="text-center">
                    <div class="icon-object border-warning-400 text-warning-400">
                        <i class="fa fa-users"></i>
                    </div>
                    <h5 class="content-group-lg">
                        {{ trans('auth.login_form.title') }}
                        <small class="display-block">{{ trans('auth.login_form.subtitle') }}</small>
                    </h5>
                </div>

                @include('vendor.form._errors')

                <div class="form-group has-feedback has-feedback-left">
                    <input type="text" id="username" name="username" class="form-control"
                           placeholder="{{ trans('auth.login_form.username_placeholder') }}"
                           value="{{ old('username') }}" required autofocus
                    />
                    <div class="form-control-feedback">
                        <i class="fa fa-user text-muted"></i>
                    </div>
                </div>

                <div class="form-group has-feedback has-feedback-left">
                    <input type="password" id="password" name="password" class="form-control"
                           placeholder="{{ trans('auth.login_form.password_placeholder') }}"
                           required
                    />
                    <div class="form-control-feedback">
                        <i class="fa fa-lock text-muted"></i>
                    </div>
                </div>

                <div class="form-group login-options">
                    <div class="row">
                        <div class="col-sm-6">
                            <label class="checkbox-inline">
                                <input type="checkbox" class="styled" id="remember" name="remember"/>
                                {{ trans('auth.login_form.remember_label') }}
                            </label>
                        </div>

                        <div class="col-sm-6 text-right">
                            {{ Html::link(route('admin.password.forgot'), trans('auth.login_form.forgotten_password')) }}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn bg-blue btn-block">
                        {{ trans('auth.login_form.btn_login') }}
                        <i class="fa fa-arrow-circle-right"></i>
                    </button>
                </div>

                <span class="help-block text-center no-margin">
                    {{ trans('auth.login_form.cookie_consent') }}
                </span>
            </div>
        {{ Form::close() }}
    </page-login>
@endsection

@push('script')
    {!! html::script(mix('js/login.page.js')) !!}
@endpush
