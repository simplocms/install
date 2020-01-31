@extends('theme_development.@layout')

@section('content')
    <section class="index-content">
        <div class="row">
            <div class="col-sm-6">
                <h2>{{ trans('theme_development.index.pages_title') }}</h2>
                @if ($pages)
                    <ul>
                        @foreach ($pages as $page)
                            <li>
                                <a href="{{ $page['url'] }}">{{ $page['name'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <em>{{ trans('theme_development.index.no_pages') }}</em>
                @endif
            </div>
            <div class="col-sm-6">
                <h2>{{ trans('theme_development.index.components_title') }}</h2>
                @if ($components)
                    <ul>
                        @foreach ($components as $component)
                            <li>
                                <a href="{{ $component['url'] }}">{{ $component['name'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <em>{{ trans('theme_development.index.no_components') }}</em>
                @endif
            </div>
        </div>
    </section>
@endsection
