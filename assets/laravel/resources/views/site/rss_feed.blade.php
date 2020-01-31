<?php
/** @var \Illuminate\Support\Collection $settings */
/** @var \App\Models\Article\Article[]|\Illuminate\Support\Collection $articles */
/** @var \App\Models\Article\Category $category */
/** @var \App\Models\Media\File $logo */
$logo = $settings->get('logo');
?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ $settings->get('site_name', trans('general.settings.site_name', [], $language->language_code)) }}</title>
        <link>{{ $homepageUrl }}</link>
        <description>{{ $settings->get('seo_description') }}</description>
        <copyright>
            &#169; {{ date('Y') }} {{ $settings->get('company_name') ?? $settings->get('site_name', trans('general.settings.site_name', [], $language->language_code)) }}
        </copyright>
        <atom:link href="{{ URL::current() }}" rel="self" type="application/rss+xml" />
        <language>{{ $language->language_code }}</language>
        <docs>http://blogs.law.harvard.edu/tech/rss</docs>
        @if ($articles->isNotEmpty())
        <lastBuildDate>{{ $articles->max('updated_at')->toRssString() }}</lastBuildDate>
        @endif
        @if ($logo)
        <image>
            <title>{{ $settings->get('site_name', trans('general.settings.site_name', [], $language->language_code)) }}</title>
            <url>{{ $logo->makeLink()->allowedFormats(['png', 'jpeg', 'gif']) }}</url>
            <link>{{ $homepageUrl }}</link>
        </image>
        @endif
        @foreach($articles as $article)
            <item>
                <title>{{ $article->seo_title ?? $article->title }}</title>
                <link>{{ $article->full_url }}</link>
                <description>{{ $article->perex }}</description>
                <guid isPermaLink="false">{{ 'a' . $article->flag_id . '-' . $article->getKey() }}</guid>
                <pubDate>{{ ($article->publish_at ?? $article->created_at)->toRssString() }}</pubDate>
                @foreach($article->categories as $category)
                    <category domain="{{ $category->full_url }}">
                        {{ $category->name }}
                    </category>
                @endforeach
                @if ($article->image)
                    <enclosure url="{{ $article->image->getUrl() }}"
                               length="{{ $article->image->size }}"
                               type="{{ $article->image->mime_type }}"/>
                @endif
            </item>
        @endforeach
    </channel>
</rss>
