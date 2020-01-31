@extends('admin.layouts.master')

@section('content')
    <v-datatable :table="{{ $table->toJson() }}"></v-datatable>

    @permission('article-flags-create')
    <a href="{{ route('admin.article_flags.create') }}" class="btn bg-teal-400 btn-labeled">
        <b class="fa fa-pencil-square-o"></b> {{ trans('admin/article_flags/general.index.btn_create') }}
    </a>
    @endpermission

@endsection

@section('breadcrumb-elements')
    @permission('article-flags-create')
    <li>
        <a href="{{ route('admin.article_flags.create') }}">
            <i class="fa fa-pencil-square-o"></i> {{ trans('admin/article_flags/general.index.btn_create') }}
        </a>
    </li>
    @endpermission
@endsection

