@extends('admin.layouts.master')

@section('content')
    <widgets-form :widget="{{ $widgetData }}"
                  :language-id="{{ $languageId }}"
                  inline-template
                  v-cloak>
        <div class='row'>
            <div class='col-md-12'>
                <div class="box-body">
                    <v-form :form="form"
                            method="POST"
                            action="{{ $submitUrl }}"
                    >

                        @include('admin.widgets._form_content')

                        <div class="form-group mt15">
                            {!! Form::button(trans($widget->exists ? 'admin/widgets/form.btn_update' : 'admin/widgets/form.btn_create'), [
                                'class' => 'btn bg-teal-400',
                                'type' => 'submit',
                                'id' => 'widgets-form-submit'
                            ]) !!}
                            <a href="{!! route('admin.widgets.index') !!}" title="Zrušit" class='btn btn-default'>
                                {{ trans('admin/widgets/form.btn_cancel') }}
                            </a>
                        </div>

                    </v-form>
                </div>
            </div>
        </div>
    </widgets-form>
@endsection

@push('style')
    @include('admin.vendor.form._styles')
@endpush

@push('script')
    @include('admin.vendor.form._scripts')
@endpush
