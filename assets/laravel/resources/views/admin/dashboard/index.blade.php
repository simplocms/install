@extends('admin.layouts.master')

@section('content')
    <div class='row'>

        @if (!$authorizeLink)
            @include('admin.dashboard._graphs')
        @else
            <div class="col-xs-12 text-center">
                @if ($canManage)
                    <a class="btn btn-primary" href="{!! $authorizeLink !!}">
                        {{ trans('admin/dashboard.btn_authorize_ga') }}
                    </a>
                @endif
            </div>
        @endif

    </div>
@endsection
