@extends('theme::layouts.main')

@section('content')

{{-- BREADCRUMB --}}
@include('theme::vendor.breadcrumbs')
{{-- /BREADCRUMB --}}

{!! $content !!}

@endsection