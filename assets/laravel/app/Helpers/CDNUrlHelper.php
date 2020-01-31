<?php declare(strict_types = 1);

namespace App\Helpers;

final class CDNUrlHelper
{
    /**
     * Make url to CDN when available.
     *
     * @param string $url
     * @return string
     */
    public static function make(string $url): string
    {
        $cdnUrl = config('app.cdn_url');
        $fullUrl = url($url);

        if (!$cdnUrl) {
            return $fullUrl;
        }

        return preg_replace(
            '/^http[s]?:\/\/.*?\/(.*)$/',
            rtrim($cdnUrl, '/') . '/$1',
            $fullUrl
        );
    }

    /**
     * Modify URLs in content
     *
     * @param string $content
     * @return string
     */
    public static function modifyContent(string $content): string
    {
        $cdnUrl = config('app.cdn_url');
        if (!$cdnUrl) {
            return $content;
        }

        foreach (config('app.cdn_paths', []) as $path) {
            $fullUrl = url($path);
            $replacement = self::make($fullUrl);

            $content = str_replace($fullUrl, $replacement, $content);
        }

        return $content;
    }
}
