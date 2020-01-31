<?php /** @var \App\Models\Web\Redirect $redirect */ ?>
@extends('admin.layouts.master')

@section('content')
    <redirects-form inline-template
                   :redirect="{{ $formValuesJson }}"
                   v-cloak
    >
        <div class='row'>
            <div class='col-md-12'>
                <div class="box-body">
                    <v-form :form="form"
                            method="{{ $redirect->exists ? 'PUT' : 'POST' }}"
                            action="{{ $submitUrl }}"
                    >
                        @include('admin.redirects._form_content')

                        <div class="form-group mt15">
                            {!! Form::button($redirect->exists ? trans('admin/redirects/form.btn_save') : trans('admin/redirects/form.btn_create'), [
                                'class' => 'btn bg-teal-400',
                                'id' => 'redirects-form-submit',
                                'type' => 'submit'
                            ]) !!}
                            <a href="{!! route('admin.redirects.index') !!}" class='btn btn-default'>
                                {!!trans('admin/redirects/form.btn_cancel')!!}
                            </a>
                        </div>
                    </v-form>
                </div>
            </div>
        </div>
    </redirects-form>
@endsection

@push('style')
    @include('admin.vendor.form._styles')
@endpush

@push('script')
    @include('admin.vendor.form._scripts')
@endpush
