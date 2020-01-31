@extends('admin.layouts.master')

@section('content')
    <languages-form inline-template>
        <div class='row'>
            <div class='col-md-12'>
                <div class="box-body">
                    {!! Form::model($language, ['url' => $submitUrl]) !!}

                    @include('admin.languages._form_content')

                    <div class="form-group mt15">
                        {!! Form::button($language->exists ? trans('admin/languages/form.btn_update') : trans('admin/languages/form.btn_create'), [
                            'class' => 'btn bg-teal-400',
                            'type' => 'submit',
                            'id' => 'submit-form-button'
                        ]) !!}
                        <a href="{!! route('admin.languages.index') !!}"
                           class='btn btn-default'>{{trans('admin/languages/form.btn_cancel')}}</a>
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </languages-form>
@endsection

@push('script')
    @include('admin.vendor.form._scripts')
@endpush
