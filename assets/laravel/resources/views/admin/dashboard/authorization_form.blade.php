@extends('admin.layouts.master')

@section('content')
    <div class='row'>

        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h2 class="panel-title">{{trans('admin/dashboard.authorization.title')}}</h2>

                    {{ Form::open(['route' => 'admin.dashboard.authorization', 'id' => 'ga-auth-form']) }}

                    <p>
                        {{ trans('admin/dashboard.authorization.text_for_link') }}
                        <a href="javascript:window.open('{!! $authUrl !!}', 'ga_auth_win', 'width=800, height=600')">
                            {{ trans('admin/dashboard.authorization.token_link_text') }}
                        </a>.
                    </p>

                    <div class="form-group required {{ $errors->has($name = 'code') ? 'has-error' : '' }}">
                        {!! Form::label($name, trans('admin/dashboard.authorization.label_token')) !!}
                        {!! Form::text($name, null, [
                            'class' => 'form-control',
                            'maxlength' => 255
                        ]) !!}
                        @include('admin.vendor.form.field_error')
                    </div>

                    <div class="form-group mt15">
                        {!! Form::button( trans('admin/dashboard.authorization.btn_authorize'), ['class' => 'btn bg-teal-400 pull-right', 'type' => 'submit'] ) !!}
                        <div class="clearfix"></div>
                    </div>
                    {{ Form::close() }}

                </div>
            </div>
        </div>


    </div>
@endsection
