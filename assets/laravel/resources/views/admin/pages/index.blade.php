<?php /** @var \App\Components\DataTables\PagesTable $table */ ?>
@extends('admin.layouts.master')

@section('content')
    <pages-index :trans="{{ json_encode(trans('admin/pages/general.index.ab_testing_stop')) }}" inline-template>
        <div>
            <v-datatable :table="{{ $table->toJson() }}"
                         v-on:stop-testing="stopTesting"
            >
                <template slot="status" slot-scope="props">
                    <span :class="['label label-' + props.row.data.statusColor]">@{{ props.column.content }}</span>
                </template>
            </v-datatable>

            <v-dialog/>
        </div>
    </pages-index>

    @permission('pages-create')
    <a href="{{ route('admin.pages.create') }}" class="btn bg-teal-400 btn-labeled">
        <b><i class="fa fa-pencil-square-o"></i></b>
        {{ trans('admin/pages/general.index.btn_create') }}
    </a>
    @endpermission

@endsection

@section('breadcrumb-elements')
    @permission('pages-create')
    <li>
        <a href="{{ route('admin.pages.create') }}">
            <i class="fa fa-pencil-square-o position-left"></i>
            {{ trans('admin/pages/general.index.btn_create') }}
        </a>
    </li>
    @endpermission
@endsection

@push('script')
    {!! Html::script(mix('js/pages-index.page.js')) !!}
@endpush
