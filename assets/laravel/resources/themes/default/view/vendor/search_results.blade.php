<?php
/** @var \Context $context */
/** @var \App\Models\Page\Page[]|\App\Structures\Paginator $pages */
$pages = $results['pages'] ?? null;
/** @var \App\Models\Article\Category[]|\Illuminate\Support\Collection $categories */
$categories = $results['categories'] ?? null;
/** @var \App\Models\Article\Article[]|\App\Structures\Paginator $articles */
$articles = $results['articles'] ?? null;
/** @var \App\Models\Photogallery\Photogallery[]|\App\Structures\Paginator $photogalleries */
$photogalleries = $results['photogalleries'] ?? null;
?>
@extends('theme::layouts.main')

@section('content')
    {{-- BREADCRUMB --}}
    @include('theme::vendor.breadcrumbs')
    {{-- /BREADCRUMB --}}

    <!-- CONTAIN -->
    <div class="container">
        <div class="row">

            <h2>
                {{ $context->trans('theme.search_page.title') }}
                ({{ $context->trans_choice('theme.search_page.results_count', $totalResults, ['count' => $totalResults]) }}
                )
            </h2>

            {{-- ARTICLES --}}
            @if ($articles)
                <h3>
                    {{ $context->trans('theme.search_page.articles_title') }}
                    ({{ $context->trans_choice('theme.search_page.results_count', $totalResults, ['count' => $results['articles']->total()]) }}
                    )
                </h3>
                <div class="list-group">
                    @foreach($articles as $article)
                        <a href="{{ $article->full_url }}" class="list-group-item">
                            <img src="{{ $article->makeImageLink('image')->fitCanvas(100, 50)->getUrl() }}" width="100">
                            <div style="display: inline-block;">
                                <strong>{{ $article->title }}</strong>
                                <p>
                                    {{ $article->perex }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
                {!! $articles->links() !!}
            @endif

            {{-- CATEGORIES --}}
            @if ($categories)
                <h4>
                    {{ $context->trans('theme.search_page.categories_title') }}
                    ({{ $context->trans_choice('theme.search_page.results_count', $totalResults, ['count' => $categories->count()]) }}
                    )
                </h4>
                @foreach($categories as $category)
                    <a href="{{ $category->full_url }}" class="btn btn-default">
                        {{ $category->name }}
                    </a>
                @endforeach
            @endif

            {{-- PAGES --}}
            @if ($pages)
                <h3>
                    {{ $context->trans('theme.search_page.pages_title') }}
                    ({{ $context->trans_choice('theme.search_page.results_count', $totalResults, ['count' => $pages->total()]) }}
                    )
                </h3>
                <div class="list-group">
                    @foreach($pages as $page)
                        <a href="{{ $page->full_url }}" class="list-group-item">
                            <img src="{{ $page->makeImageLink('image')->fitCanvas(100, 50)->getUrl() }}" width="100">
                            <div style="display: inline-block;">
                                <strong>{{ $page->seo_title ?? $page->name }}</strong>
                            </div>
                        </a>
                    @endforeach
                </div>
                {!! $pages->links() !!}
            @endif

            {{-- PHOTOGALLERIES --}}
            @if ($photogalleries)
                <h3>
                    {{ $context->trans('theme.search_page.photogalleries_title') }}
                    ({{ $context->trans_choice('theme.search_page.results_count', $totalResults, ['count' => $photogalleries->total()]) }}
                    )
                </h3>
                <div class="list-group">
                    @foreach($photogalleries as $photogallery)
                        <a href="{{ $photogallery->full_url }}" class="list-group-item">
                            <div style="display: inline-block;">
                                <strong>{{ $photogallery->seo_title ?? $photogallery->name }}</strong>
                            </div>
                        </a>
                    @endforeach
                </div>
                {!! $photogalleries->links() !!}
            @endif

            @if(!$totalResults)
                <h4><em>{{ $context->trans('articles.no_articles') }}</em></h4>
            @endif

        </div>
    </div>

@endsection
