<?php declare(strict_types = 1);

namespace App\Services\MediaLibrary;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

final class ImageOptimizer
{
    /** @var int Optimization timeout in seconds */
    private const TIMEOUT = 20;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Optimize specified image.
     *
     * @param string $path
     * @param string|null $mime
     */
    public function optimize(string $path, string $mime = null): void
    {
        if ($mime === null) {
            $mime = mime_content_type($path);
        }

        switch ($mime) {
            case 'image/jpeg':
                $this->optimizeJPEG($path);
                return;
            case 'image/png':
                self::optimizePNG($path);
                return;
            case 'image/gif':
                self::optimizeGIF($path);
                return;
            case 'image/svg':
            case 'image/svg+xml':
            case 'text/html':
            case 'text/plain':
                self::optimizeSVG($path);
                return;
        }
    }


    /**
     * Optimize JPEG images with mozjpeg.
     *
     * @param string $path
     */
    protected function optimizeJPEG(string $path): void
    {
        $this->filesystem->copy($path, $clone = $path . '.opt');

        self::runOptimizer('mozjpeg', [
            '-outfile ' . escapeshellarg($path),
            '-quality 85',
            escapeshellarg($clone)
        ]);

        $this->filesystem->delete($clone);
    }


    /**
     * Optimize PNG images.
     *
     * Pngquant 2 manual: https://pngquant.org/
     * Optipng: http://optipng.sourceforge.net/
     *
     * @param string $path
     */
    protected static function optimizePNG(string $path): void
    {
        self::runOptimizer('pngquant', [
            '--force',
            escapeshellarg($path),
            '--output=' . escapeshellarg($path),
        ]);

        self::runOptimizer('optipng', [
            '-i0',
            '-o2',
            '-quiet',
            escapeshellarg($path)
        ]);
    }


    /**
     * Optimize GIF images.
     *
     * Gifsicle: http://www.lcdf.org/gifsicle/
     *
     * @param string $path
     */
    protected static function optimizeGIF(string $path): void
    {
        self::runOptimizer('gifsicle', [
            '-b',
            '-O3',
            escapeshellarg($path)
        ]);
    }


    /**
     * Optimize SVGs.
     *
     * SVGO: https://github.com/svg/svgo
     *
     * @param string $path
     */
    protected static function optimizeSVG(string $path): void
    {
        if (strtolower(pathinfo($path, PATHINFO_EXTENSION)) !== 'svg') {
            return;
        }

        self::runOptimizer('svgo', [
            '--disable=cleanupIDs',
            '--input=' . escapeshellarg($path),
            '--output=' . escapeshellarg($path),
        ]);
    }


    /**
     * Run optimizer.
     *
     * @param string $binary
     * @param array $options
     */
    protected static function runOptimizer(string $binary, array $options = []): void
    {
        $optionString = implode(' ', $options);

        $command = "\"{$binary}\" {$optionString}";
        $process = Process::fromShellCommandline($command);
        $process->setTimeout(self::TIMEOUT);

        try {
            $process->run();
        } catch (ProcessTimedOutException $exception) {
            // do nothing
        }
    }
}
