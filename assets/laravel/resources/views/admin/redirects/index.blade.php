@extends('admin.layouts.master')

@section('content')
    <v-datatable :table="{{ $table->toJson() }}">
        <template slot="from" slot-scope="props">
            @{{ props.column.content }}
            <a :href="props.row.data.urlFrom" target="_blank" @click.stop>
                <i class="fa fa-external-link-square"></i>
            </a>
        </template>
    </v-datatable>

    @permission('redirects-create')
    <a href="{{ route('admin.redirects.create') }}" class="btn bg-teal-400 btn-labeled">
        <b><i class="fa fa-pencil-square-o"></i></b>
        {{ trans('admin/redirects/general.index.btn_create') }}
    </a>

    <a href="{{ route('admin.redirects.bulk_create') }}" class="btn btn-primary btn-labeled">
        <b><i class="fa fa-stack-exchange"></i></b>
        {{ trans('admin/redirects/general.index.btn_bulk_create') }}
    </a>
    @endpermission

    <a href="{{ route('admin.redirects.export') }}" class="btn btn-primary btn-labeled">
        <b><i class="fa fa-download"></i></b>
        {{ trans('admin/redirects/general.index.btn_export') }}
    </a>

@endsection

@section('breadcrumb-elements')
    @permission('redirects-create')
    <li>
        <a href="{{ route('admin.redirects.create') }}">
            <i class="fa fa-pencil-square-o position-left"></i>
            {{ trans('admin/redirects/general.index.btn_create') }}
        </a>
    </li>
    <li>
        <a href="{{ route('admin.redirects.bulk_create') }}">
            <b><i class="fa fa-stack-exchange"></i></b>
            {{ trans('admin/redirects/general.index.btn_bulk_create') }}
        </a>
    </li>
    @endpermission
    <li>
        <a href="{{ route('admin.redirects.export') }}">
            <b><i class="fa fa-download"></i></b>
            {{ trans('admin/redirects/general.index.btn_export') }}
        </a>
    </li>
@endsection
