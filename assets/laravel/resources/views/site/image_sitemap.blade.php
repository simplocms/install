<?php /** @var \App\Models\Media\File[] $images */ ?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    <url>
        <loc>{{ \App\Structures\Enums\SingletonEnum::urlFactory()->getHomepageUrl() }}</loc>

        @foreach($images as $image)
        <image:image>
            <image:loc>{{ $image->getUrl() }}</image:loc>
            @if (!is_null($image->description))
                <image:caption>{{ $image->description }}</image:caption>
            @endif
        </image:image>
        @endforeach
    </url>
</urlset>
