@extends('layouts.error')

@section('code', '503')
@section('title', trans('general.error_pages.503.title'))

@section('image')
    <div style="background-image: url('{{ asset('/media/admin/images/errors/503.svg') }}');"
         class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center">
    </div>
@endsection

@section('message', __($exception->getMessage() ?: trans('general.error_pages.503.info')))
