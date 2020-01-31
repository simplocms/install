<?php /** @var \App\Models\Page\Page $page */ ?>
@extends('admin.layouts.master')

@section('content')
    <pages-form inline-template
                @if ($page->hasTestingCounterpart())
                :active-testing-variant-id="{{ $page->getKey() ?? 0 }}"
                :testing-counterpart="{{ json_encode($testing) }}"
                @endif
                :page="{{ $formValuesJson }}"
                :parent-pages="{{ json_encode($parentPages) }}"
                :trans="{{ \App\Helpers\Functions::combineTransToJson(['admin/pages/form']) }}"
                submit-url="{{ $submitUrl }}"
                v-cloak
    >
        <div class='row'>
            <div class='col-md-12'>
                <div class="box-body">
                    <v-form :form="form"
                            method="POST"
                            :action="innerSubmitUrl"
                            @success="onSuccess"
                    >
                        @include('admin.pages.form.layout')

                        <div class="form-group mt15">
                            {!! Form::button($page->exists ? trans('admin/pages/form.btn_save_finish') : trans('admin/pages/form.btn_create'), [
                                'class' => 'btn bg-teal-400',
                                'id' => 'pages-form-submit',
                                'type' => 'submit'
                            ]) !!}
                            @if ($page->exists)
                                {!! Form::button(trans('admin/pages/form.btn_save'), [
                                    'class' => 'btn bg-teal-400',
                                    'id' => 'pages-form-save',
                                    'type' => 'submit',
                                    '@click' => 'isSaving = true'
                                ]) !!}
                            @endif
                            <a href="{!! route('admin.pages.index') !!}" class='btn btn-default'>
                                {!!trans('admin/pages/form.btn_cancel')!!}
                            </a>
                        </div>
                    </v-form>
                </div>
            </div>
        </div>
    </pages-form>
@endsection

@push('style')
    @include('admin.vendor.form._styles')
@endpush

@push('script')
    @include('admin.vendor.form._scripts')
@endpush
