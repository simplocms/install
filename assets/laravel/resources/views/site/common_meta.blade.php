<?php /** @var \App\Models\Web\ViewData $data */ ?>
@foreach($data->getMetaTags() as $tag => $value)
    @if (is_array($value))
        @foreach($value as $subValue)
            <meta name="{{ $tag }}" content="{{ $subValue }}">
        @endforeach
    @else
        <meta name="{{ $tag }}" content="{{ $value }}">
    @endif
@endforeach
@foreach(\App\Structures\Enums\SingletonEnum::responseManager()->getLinks() as $link)
    {!! $link->getMetaLink() !!}
@endforeach
<link rel="alternate" type="application/rss+xml" href="{{ UrlFactory::getRssFeedUrl() }}">
