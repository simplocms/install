<?php /** @var \App\Components\DataTables\PagesTable $table */ ?>
@extends('admin.layouts.master')

@section('content')
    <v-datatable :table="{{ $table->toJson() }}">
        <template slot="status" slot-scope="props">
            <span :class="['label label-' + props.row.data.statusColor]">@{{ props.column.content }}</span>
        </template>
    </v-datatable>

    @permission(('article-categories-create'))
    <a href="{{ route('admin.categories.create',$flag->url) }}" class="btn bg-teal-400 btn-labeled">
        <b class="fa fa-pencil-square-o"></b> {{trans('admin/category/general.index.btn_create')}}
    </a>
    @endpermission

@endsection

@section('breadcrumb-elements')
    @permission(('article-categories-create'))
    <li>
        <a href="{{ route('admin.categories.create', $flag->url) }}">
            <i class="fa fa-pencil-square-o position-left"></i> {{trans('admin/category/general.index.btn_create')}}
        </a>
    </li>
    @endpermission
@endsection
