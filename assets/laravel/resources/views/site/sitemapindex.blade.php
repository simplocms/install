<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach($languages as $language)
        <sitemap>
            <loc>{{ route('sitemap', $language->language_code, true) }}</loc>
        </sitemap>
    @endforeach
    @for ($i = 1; $i <= $imagePagesCount; $i++)
        <sitemap>
            <loc>{{ route('sitemap.images', $i) }}</loc>
        </sitemap>
    @endfor
</sitemapindex>
