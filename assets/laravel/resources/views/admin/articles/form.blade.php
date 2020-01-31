<?php /** @var \App\Models\Article\Article $article */ ?>
@extends('admin.layouts.master')

@section('content')
    <articles-form inline-template
                   categories-tree-url="{{ $categoriesTreeUrl }}"
                   :article="{{ $formDataJson }}"
                   :use-tags="{{ $flag->use_tags ? 'true' : 'false' }}"
                   :use-grid-editor="{{ $flag->use_grid_editor ? 'true' : 'false' }}"
                   :tags="{{ $flag->use_tags ? $tags->toJson() : '[]' }}"
                   :categories="{{ $articleCategories }}"
                   v-cloak
    >
        <div class="box-body">
            <v-form :form="form"
                    method="POST"
                    id="articles-form"
                    action="{{ $submitUrl }}"
            >
                @include('admin.articles.form.layout')

                <div class="form-group mt15">
                    {!! Form::button($article->exists ? trans('admin/article/form.btn_update') : trans('admin/article/form.btn_create'), [
                        'class' => 'btn bg-teal-400',
                        'type' => 'submit'
                    ]) !!}

                    <a href="{{ URL::previous() }}" class='btn btn-default'>
                        {{ trans('admin/article/form.btn_cancel') }}
                    </a>
                </div>

            </v-form>
        </div>
    </articles-form>
@endsection

@push('style')
    @include('admin.vendor.form._styles')
@endpush

@push('script')
    @include('admin.vendor.form._scripts')
@endpush
