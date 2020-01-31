@extends('admin.layouts.master')

@section('content')
    <categories-form :category="{{ $formDataJson }}"
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

                        @include('admin.categories._form_content')

                        <div class="form-group mt15">
                            {!! Form::button($category->exists ? trans('admin/category/form.btn_update') : trans('admin/category/form.btn_create'), [
                                'class' => 'btn bg-teal-400',
                                'id' => 'btn-submit-edit',
                                'type' => 'submit'
                            ]) !!}
                            <a href="{!! $cancelUrl !!}" class='btn btn-default'>
                                {{ trans('admin/category/form.btn_cancel') }}
                            </a>
                        </div>

                    </v-form>
                </div>
            </div>
        </div>
    </categories-form>
@endsection

@push('script')
    <script>
        window.categoriesFormOptions = function () {
            return {
                data: {!! $category->getFormAttributesJson(['name', 'url']) !!}
            }
        };
    </script>

    @include('admin.vendor.form._scripts')
@endpush
