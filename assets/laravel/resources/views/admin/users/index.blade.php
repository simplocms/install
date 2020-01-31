@extends('admin.layouts.master')

@section('breadcrumb-elements')
    @permission('users-create')
    <li>
        <a href="{!! route('admin.users.create') !!}">
            <i class="fa fa-pencil-square-o position-left"></i> {{ trans('admin/users/general.index.btn_create') }}
        </a>
    </li>
    @endpermission
@endsection

@section('content')

    <v-datatable :table="{{ $table->toJson() }}">
        <template slot="toggle" slot-scope="props">
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

    @permission('users-create')
    <a href="{{ route('admin.users.create') }}" class="btn bg-teal-400 btn-labeled">
        <b class="fa fa-pencil-square-o"></b>
        {{ trans('admin/users/general.index.btn_create') }}
    </a>
    @endpermission
@endsection
