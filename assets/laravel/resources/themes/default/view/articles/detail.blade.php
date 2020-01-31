<?php /** @var \App\Models\Article\Article $article */ ?>
@extends('theme::layouts.main')

@section('content')

    {{-- BREADCRUMB --}}
    @include('theme::vendor.breadcrumbs') 
    {{-- /BREADCRUMB --}}
 
    <!-- CONTAIN -->
    <div class="container">
        <div class="row">

            <div class="blog-post">
                <h2 class="blog-post-title">{{ $article->title }}</h2>
                <p class="blog-post-meta">{{ $article->publish_at->format('j.n.Y H:i') }}</p>

                <p>{{ $article->perex }}</p> 
                <hr>
                {!! $article->getContent() !!}
                <hr>
                @foreach($article->tags as $tag)
                <a href="{{ route('homepage', [
                    'url' => $articlesPageUrl,
                    'tag' => $tag->name
                ]) }}" class="label label-info">{{ $tag->name }}</a>
                @endforeach
            </div>
        </div>
    </div>

@endsection
