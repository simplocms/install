@extends('theme_development.@layout')

@section('content')
    <nav class="navbar">
        <div class="container-fluid">
            <ul class="nav navbar-nav">
                <li>
                    <a href="{{ route('theme_development.index') }}" class="btn btn-link">
                        <i class="fa fa-arrow-left"></i>
                        {{ trans('theme_development.btn_list') }}
                    </a>
                </li>
                <li class="info">
                    {{ trans('theme_development.page_info', compact('name')) }}
                </li>
            </ul>
        </div>
    </nav>

    <section class="page-content">
        <iframe src="{!! route('theme_development.page', ['name' => $name, 'preview']) !!}"></iframe>
    </section>
@endsection
