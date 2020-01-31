<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml/">
    <url>
        <loc>{!! \App\Structures\Enums\SingletonEnum::urlFactory()->getHomepageUrl($language) !!}</loc>
        <priority>1.0</priority>
        @foreach($alternateLanguages as $alternateLanguage)
            <xhtml:link rel="alternate"
                        hreflang="{{ $alternateLanguage->language_code }}"
                        href="{!! \App\Structures\Enums\SingletonEnum::urlFactory()->getHomepageUrl($alternateLanguage) !!}"/>
        @endforeach
    </url>

    @foreach ($urlSet as $url)
        <url>
            <loc>{{ $url->loc }}</loc>
            <lastmod>{{ $url->lastmod->tz('UTC')->toAtomString() }}</lastmod>
            <changefreq>{{ $url->changefreq }}</changefreq>
            <priority>{{ $url->priority }}</priority>
        </url>
    @endforeach
</urlset>
