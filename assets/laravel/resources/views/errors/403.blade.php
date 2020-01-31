@extends('layouts.error')

@section('code', '403')
@section('title', trans('general.error_pages.403.title'))

@section('image')
    <div style="background-image: url('{{ asset('/media/admin/images/errors/403.svg') }}');"
         class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center"
    >
    </div>
@endsection

@section('message', __($exception->getMessage() ?: trans('general.error_pages.403.info')))
