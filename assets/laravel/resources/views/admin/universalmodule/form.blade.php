<?php
/** @var \App\Models\UniversalModule\UniversalModuleItem $moduleData */
/** @var \App\Services\UniversalModules\UniversalModule $module */
?>
@extends('admin.layouts.master')

@section('content')
    <universalmodule-form inline-template
                          :content="{{ json_encode((object)$content) }}"
                          :item="{{ $itemData }}"
                          :fields="{{ json_encode($module->getFields()) }}"
                          ck-editor-uri="{!! url("plugin/js/ckeditor.js") !!}"
    >
        <div class='row'>
            <div class='col-md-12'>
                <v-form :form="form"
                        method="POST"
                        action="{{ $submitUrl }}"
                >

                    @include('admin.universalmodule.form.layout')

                    <div class="form-group mt15">
                        {!! Form::button($moduleData->exists ? trans('admin/universal_modules.form.btn_update') : trans('admin/universal_modules.form.btn_create'), [
                            'class' => 'btn bg-teal-400',
                            'type' => 'submit',
                        ]) !!}
                        <a href="{!! route('admin.universalmodule.index', $prefix) !!}"
                           class='btn btn-default'
                        >{{ trans('admin/universal_modules.form.btn_cancel') }}</a>
                    </div>

                </v-form>
            </div>
        </div>
    </universalmodule-form>
@endsection

@push('script')
    @include('admin.vendor.form._scripts')
@endpush
