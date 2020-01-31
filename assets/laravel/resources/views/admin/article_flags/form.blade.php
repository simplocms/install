@extends('admin.layouts.master')

@section('content')
    <article-flags-page :flag="{{ $formDataJson }}"
                        inline-template
                        v-cloak
    >
        <div class='row'>
            <div class='col-md-12'>
                <div class="box-body">

                    <v-form :form="form"
                            method="POST"
                            action="{{ $submitUrl }}"
                    >
                        @include('admin.article_flags._form_content')

                        <div class="form-group mt15">
                            {!! Form::button($flag->exists ? trans('admin/article_flags/form.btn_update') : trans('admin/article_flags/form.btn_create'), [
                                'class' => 'btn bg-teal-400',
                                'type' => 'submit'
                            ]) !!}
                            <a href="{{ URL::previous() }}" class='btn btn-default'>
                                {{ trans('admin/article_flags/form.btn_cancel') }}
                            </a>
                        </div>
                    </v-form>

                </div>
            </div>
        </div>
    </article-flags-page>
@endsection

@push('script')
    @include('admin.vendor.form._scripts')
@endpush
