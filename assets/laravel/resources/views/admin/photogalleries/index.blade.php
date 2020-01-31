<?php /** @var \App\Components\DataTables\PagesTable $table */ ?>
@extends('admin.layouts.master')

@section('content')
    <v-datatable :table="{{ $table->toJson() }}">
        <template slot="status" slot-scope="props">
            <span :class="['label label-' + props.row.data.statusColor]">@{{ props.column.content }}</span>
        </template>
    </v-datatable>

    @permission('photogalleries-create')
    <a href="{{ route('admin.photogalleries.create') }}" class="btn bg-teal-400 btn-labeled">
        <b class="fa fa-pencil-square-o"></b>
        {{ trans('admin/photogalleries/general.index.btn_create') }}
    </a>
    @endpermission

@endsection

@section('breadcrumb-elements')
    @permission('photogalleries-create')
    <li>
        <a href="{{ route('admin.photogalleries.create') }}">
            <i class="fa fa-pencil-square-o"></i>
            {{ trans('admin/photogalleries/general.index.btn_create') }}
        </a>
    </li>
    @endpermission
@endsection
