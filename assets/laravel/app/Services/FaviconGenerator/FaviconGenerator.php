<?php

namespace App\Services\FaviconGenerator;

use App\Models\Media\File;
use Intervention\Image\Constraint;

final class FaviconGenerator
{
    private const DEFAULT_PNG_FAVICON_SIZE = 16;
    private const PREFIX_APPLE_TOUCH = 'apple-touch-icon';
    private const PREFIX_ANDROID_CHROME = 'android-chrome';
    private const SAFARI_PINNED_TAB = 'safari-pinned-tab.svg';
    private const PREFIX_MS_TILE = 'mstile';

    /**
     * @var \App\Models\Media\File
     */
    private $file;

    /**
     * FaviconGenerator constructor.
     * @param \App\Models\Media\File $file
     */
    public function __construct(File $file)
    {
        $this->file = $file;
    }


    /**
     * Process icon image and generate icons.
     */
    private function process(): void
    {
        self::clear();

        if (!$this->file->isProcessableImage()) {
            return;
        }

        try {
            // Favicon
            foreach (config('admin.icon_sizes.png_favicons') ?? [] as $size) {
                $this->makePngFavicon(self::getWidth($size), self::getHeight($size));
            }

            if (self::hasImagick()) {
                $this->makeCombinedFavicon(config('admin.icon_sizes.main') ?? []);
            } else {
                $this->makePngFavicon(
                    self::DEFAULT_PNG_FAVICON_SIZE,
                    self::DEFAULT_PNG_FAVICON_SIZE,
                    'favicon'
                );
            }

            // Apple touch
            foreach (config('admin.icon_sizes.apple_touch') ?? [] as $size) {
                $this->makeAppleTouchIcon(self::getWidth($size), self::getHeight($size));
            }

            $this->makeAppleTouchIcon(180, 180, self::PREFIX_APPLE_TOUCH);

            // Android Chrome
            foreach (config('admin.icon_sizes.android_chrome') ?? [] as $size) {
                $this->makeAndroidChromeIcon(self::getWidth($size), self::getHeight($size));
            }

            // MsTile
            foreach (config('admin.icon_sizes.ms_tile') ?? [] as $size) {
                $this->makeMsTile(self::getWidth($size), self::getHeight($size));
            }

            if ($this->file->isSvg()) {
                file_put_contents(public_path(self::SAFARI_PINNED_TAB), $this->file->getFileData());
            }
        } catch (\Exception $e) {
            return;
        }
    }


    /**
     * Clear all icon files.
     */
    public static function clear(): void
    {
        \File::delete(\File::glob(public_path(self::PREFIX_ANDROID_CHROME . '*.png')));
        \File::delete(\File::glob(public_path(self::PREFIX_APPLE_TOUCH . '*.png')));
        \File::delete(\File::glob(public_path(self::PREFIX_MS_TILE . '*.png')));
        \File::delete(\File::glob(public_path('favicon*')));
        \File::delete(public_path(self::SAFARI_PINNED_TAB));
    }


    /**
     * @return array
     */
    public static function getLinks(): array
    {
        $links = [];

        // Favicon
        foreach (config('admin.icon_sizes.png_favicons') ?? [] as $size) {
            $sizeString = self::getWidth($size) . 'x' . self::getHeight($size);
            $fileName = "favicon-$sizeString.png";
            if (!file_exists(public_path($fileName))) {
                continue;
            }
            $links[] = [
                'rel' => 'icon',
                'type' => 'image/png',
                'sizes' => $sizeString,
                'href' => asset($fileName)
            ];
        }

        // "When favicon is in the root directory, we do not include link. This is because it somehow confuses
        // some other browsers like Chrome. And since IE looks for a file named favicon.ico anyway,
        // the best solution is to not even talk about it." - https://realfavicongenerator.net/faq
        if (file_exists(public_path("favicon.png")) && !file_exists(public_path("favicon.ico"))) {
            $size = self::DEFAULT_PNG_FAVICON_SIZE . 'x' . self::DEFAULT_PNG_FAVICON_SIZE;
            $links[] = [
                'rel' => 'icon',
                'type' => 'image/png',
                'sizes' => $size,
                'href' => asset("favicon.png")
            ];
        }

        // Apple touch
        $appleTouchSizes = config('admin.icon_sizes.apple_touch') ?? [];
        foreach ($appleTouchSizes as $index => $size) {
            $sizeString = self::getWidth($size) . 'x' . self::getHeight($size);
            $fileName = self::PREFIX_APPLE_TOUCH . "-$sizeString.png";
            if (!file_exists(public_path($fileName))) {
                continue;
            }

            $links[] = [
                'rel' => 'apple-touch-icon',
                'type' => 'image/png',
                'sizes' => $sizeString,
                'href' => asset($fileName)
            ];
        }

        return $links;
    }


    /**
     * @return array|null
     */
    public static function getSafariPinedTabLink(): ?array
    {
        if (file_exists(public_path(self::SAFARI_PINNED_TAB))) {
            return [
                'rel' => 'mask-icon',
                'href' => asset(self::SAFARI_PINNED_TAB)
            ];
        }

        return null;
    }


    /**
     * @return array
     */
    public static function getWebManifestIcons(): array
    {
        $icons = [];

        foreach (config('admin.icon_sizes.android_chrome') ?? [] as $size) {
            $sizeString = self::getWidth($size) . 'x' . self::getHeight($size);
            $fileName = self::PREFIX_ANDROID_CHROME . "-$sizeString.png";
            if (!file_exists(public_path($fileName))) {
                continue;
            }

            $icons[] = [
                'src' => asset($fileName),
                'sizes' => $sizeString,
                'type' => 'image/png',
            ];
        }

        return $icons;
    }


    /**
     * @return array
     */
    public static function getMsTileIcons(): array
    {
        $icons = [];

        foreach (config('admin.icon_sizes.ms_tile') ?? [] as $size) {
            $sizeString = self::getWidth($size) . 'x' . self::getHeight($size);
            $fileName = "mstile-$sizeString.png";
            if (!file_exists(public_path($fileName))) {
                continue;
            }

            $icons[] = [
                'element' => "square{$sizeString}logo",
                'src' => asset($fileName)
            ];
        }

        return $icons;
    }


    /**
     * @param int|int[] $size
     * @return int
     */
    private static function getWidth($size): int
    {
        return intval(is_array($size) ? $size[0] : $size);
    }


    /**
     * @param int|int[] $size
     * @return int
     */
    private static function getHeight($size): int
    {
        return intval(is_array($size) ? $size[1] : $size);
    }


    /**
     * Generate icons.
     *
     * @param \App\Models\Media\File $file
     */
    public static function generate(File $file): void
    {
        $generator = new self($file);
        $generator->process();
    }


    /**
     * @param array $sizes
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \ImagickException
     */
    private function makeCombinedFavicon(array $sizes)
    {
        $favicon = new \Imagick();
        $favicon->setFormat("ico");

        foreach ($sizes as $size) {
            $width = self::getWidth($size);
            $height = self::getHeight($size);
            $image = \Image::make($this->file->getFileData());
            $image->resize(
                $width, $height,
                function (Constraint $constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                }
            )->resizeCanvas($width, $height, 'center');
            /** @var \Imagick $core */
            $core = $image->encode('png', 100)->getCore();
            $favicon->addImage(clone $core);
            $image->destroy();
        }

        $favicon->writeImages(public_path("favicon.ico"), true);
        $favicon->destroy();
    }


    /**
     * @param int $width
     * @param $height
     * @param string|null $forceName
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function makePngFavicon(int $width, int $height, string $forceName = null): void
    {
        $this->makePNG($width, $height, $forceName ?? "favicon-{$width}x{$height}");
    }


    /**
     * @param int $width
     * @param int $height
     * @param string|null $forceName
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function makeAppleTouchIcon(int $width, int $height, string $forceName = null): void
    {
        $this->makePNG($width, $height, $forceName ?? self::PREFIX_APPLE_TOUCH . "-{$width}x{$height}");
    }


    /**
     * @param int $width
     * @param int $height
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function makeAndroidChromeIcon(int $width, int $height): void
    {
        $this->makePNG($width, $height, self::PREFIX_ANDROID_CHROME . "-{$width}x{$height}");
    }


    /**
     * @param int $width
     * @param int $height
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function makeMsTile(int $width, int $height): void
    {
        $this->makePNG($width, $height, self::PREFIX_MS_TILE . "-{$width}x{$height}");
    }


    /**
     * @param int $width
     * @param int $height
     * @param string $name
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function makePNG(int $width, int $height, string $name): void
    {
        $image = \Image::make($this->file->getFileData());
        $padding = 4;

        $image->resize($width - $padding, $height - $padding,
            function (Constraint $constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            }
        )->resizeCanvas($width, $height, 'center');

        \Image::canvas($width, $height, null)
            ->insert($image)
            ->encode('png', 100)
            ->save(public_path("$name.png"), 100);
    }


    /**
     * @return bool
     */
    private static function hasImagick(): bool
    {
        return extension_loaded('imagick');
    }
}
