<?php /** @var \App\Components\DataTables\PagesTable $table */ ?>
@extends('admin.layouts.master')

@section('content')
    <v-datatable :table="{{ $table->toJson() }}"></v-datatable>

    @permission('widgets-create')
    <a href="{{ route('admin.widgets.create') }}" class="btn bg-teal-400 btn-labeled">
        <b><i class="fa fa-pencil-square-o"></i></b>
        {{ trans('admin/widgets/general.index.btn_create') }}
    </a>
    @endpermission

@endsection

@section('breadcrumb-elements')
    @permission('widgets-create')
    <li>
        <a href="{{ route('admin.widgets.create') }}">
            <i class="fa fa-pencil-square-o position-left"></i>
            {{ trans('admin/widgets/general.index.btn_create') }}
        </a>
    </li>
    @endpermission
@endsection
