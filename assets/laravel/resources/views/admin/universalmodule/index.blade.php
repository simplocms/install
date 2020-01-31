<?php /** @var string $prefix */ ?>

@extends('admin.layouts.master')

@section('content')
    <v-datatable :table="{{ $table->toJson() }}">
        <template slot="__toggle" slot-scope="props">
            <v-automatic-post :url="props.row.data.toggleUrl"
                              :title="props.column.content"
                              v-if="props.row.data.toggleEnabled"
            >
                <i class="fa-2x fa fa-check-circle-o text-success" v-if="props.row.data.enabled"></i>
                <i class="fa-2x fa fa-ban text-danger" v-else></i>
            </v-automatic-post>
            <span v-else>
                <i class="fa-2x fa fa-check-circle-o" v-if="props.row.data.enabled"></i>
                <i class="fa-2x fa fa-ban" v-else></i>
            </span>
        </template>
    </v-datatable>

    @permission("universal_module_$prefix-create")
    <a href="{{ route('admin.universalmodule.create', $prefix) }}" class="btn bg-teal-400 btn-labeled">
        <b><i class="fa fa-pencil-square-o"></i></b> {{ trans('admin/universal_modules.index.btn_create') }}
    </a>
    @endpermission
@endsection

@section('breadcrumb-elements')
    @permission("universal_module_$prefix-create")
    <li>
        <a href="{{ route('admin.universalmodule.create', $prefix) }}">
            <i class="fa fa-pencil-square-o position-left"></i> {{ trans('admin/universal_modules.index.btn_create') }}
        </a>
    </li>
    @endpermission
@endsection
