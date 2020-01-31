@extends('admin.layouts.master')

@section('content')
    <photogalleries-form inline-template
                         :photogallery="{{ $formDataJson }}"
                         v-cloak
    >
        <div class='row'>
            <div class='col-md-12'>
                <div class="box-body">
                    <v-form :form="form"
                            method="POST"
                            id="photogalleries-form"
                            action="{{ $submitUrl }}"
                    >
                        @include('admin.photogalleries._form_content')

                        <div class="form-group mt15">
                            {!! Form::button($photogallery->exists ? trans('admin/photogalleries/form.btn_update') : trans('admin/photogalleries/form.btn_create'), [
                                'class' => 'btn bg-teal-400',
                                'type' => 'submit'
                            ] ) !!}
                            <a href="{{ URL::previous() }}" class='btn btn-default'>
                                {{ trans('admin/photogalleries/form.btn_cancel') }}
                            </a>
                        </div>
                    </v-form>
                </div>
            </div>
        </div>
    </photogalleries-form>
@endsection

@push('script')
    @include('admin.vendor.form._scripts')
@endpush
