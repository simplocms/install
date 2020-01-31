@extends('layouts.error')

@section('code', '500')
@section('title', trans('general.error_pages.500.title'))

@section('image')
    <div style="background-image: url('{{ asset('/media/admin/images/errors/500.svg') }}');"
         class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center">
    </div>
@endsection

@section('message', trans('general.error_pages.500.info'))
