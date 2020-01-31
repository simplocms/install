@extends('layouts.error')

@section('code', '404')
@section('title', trans('general.error_pages.404.title'))

@section('image')
    <div style="background-image: url('{{ asset('/media/admin/images/errors/404.svg') }}');"
         class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center"
    ></div>
@endsection

@section('message', trans('general.error_pages.404.info'))
