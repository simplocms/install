<?php
/** @var \App\Models\ContextBase $context */
$color = \App\Structures\Enums\SingletonEnum::settings()->get('theme_color', '#ffffff');
$safariPinnedTab = \App\Services\FaviconGenerator\FaviconGenerator::getSafariPinedTabLink();
?>

@foreach(\App\Services\FaviconGenerator\FaviconGenerator::getLinks() as $link)
    <link rel="{{ $link['rel'] }}" type="{{ $link['type'] }}" sizes="{{ $link['sizes'] }}" href="{{ $link['href'] }}"/>
@endforeach

@if ($safariPinnedTab)
    <link rel="{{ $safariPinnedTab['rel'] }}" href="{{ $safariPinnedTab['href'] }}" color="{{ $color }}">
@endif

<link rel="manifest" href="{{ route('site.webmanifest', $context->getLanguage()->language_code) }}">
<meta name="msapplication-TileColor" content="{{ $color }}">
<meta name="theme-color" content="{{ $color }}">
