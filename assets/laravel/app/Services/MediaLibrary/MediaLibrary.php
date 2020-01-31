<?php
/**
 * MediaLibrary.php created by Patrik Václavek
 */

namespace App\Services\MediaLibrary;

use App\Models\Media\Directory;
use App\Models\Media\File;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaLibrary
{
    const ROOT_DIRECTORY = 'media-library';

    const TEMP_DIRECTORY = '_temp';

    const CACHE_DIRECTORY = '_cache';

    const DONE_DIRECTORY = 'done';

    const MAX_TEMP_FILE_AGE = 3600; // seconds

    const MAX_DONE_FILE_AGE = 86400; // seconds

    /**
     * Filesystem storage instance.
     *
     * @var \Illuminate\Filesystem\FilesystemAdapter
     */
    protected $storage;

    /**
     * Filesystem disk.
     *
     * @var string
     */
    protected $disk;

    /**
     * @var \App\Services\MediaLibrary\ImageOptimizer
     */
    private $imageOptimizer;

    /**
     * Initialize media library service for specified storage.
     * @param string $disk
     */
    public function __construct(string $disk)
    {
        $this->disk = $disk;
        $this->storage = \Storage::disk($disk);
        $this->imageOptimizer = app()->make(ImageOptimizer::class);
    }


    /**
     * Get file data (content).
     *
     * @param string $path
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getFileData(string $path): string
    {
        return $this->storage->get($this->getStoragePath($path));
    }


    /**
     * Update file data (content).
     *
     * @param string $path
     * @param string $data
     * @return bool
     */
    public function updateFileData(string $path, string $data): bool
    {
        $this->clearFileCache($path);
        return $this->storage->put($this->getStoragePath($path), $data);
    }


    /**
     * Clear file cache.
     *
     * @param string $path - short path
     */
    public function clearFileCache(string $path): void
    {
        FileCache::forFile($path)->clearCache();
    }


    /**
     * Save uploaded file to specified path.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $path - must include filename
     * @return false|string
     */
    public function saveUploadedFile(UploadedFile $file, string $path)
    {
        $result = $this->getStorage()->putFileAs($this->getStoragePath(dirname($path)), $file, basename($path));
        $this->getLocalStorage()->delete(
            $this->getTempPath(self::DONE_DIRECTORY . '/' . $file->getFilename())
        );
        return $result;
    }


    /**
     * Get mime type of the specified file.
     *
     * @param string $path
     * @return null|string
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function getFileMimeType(string $path): ?string
    {
        return $this->storage->getMimetype($this->getStoragePath($path)) ?: null;
    }


    /**
     * Check if specified file is supported image.
     *
     * @param string $path
     * @return bool
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function isFileProcessable(string $path): bool
    {
        $mimeType = $this->getFileMimeType($path);
        return $mimeType ? $this->isMimeProcessableFile($mimeType) : false;
    }


    /**
     * Create instance of link builder.
     *
     * @param \App\Models\Media\File $file
     * @return \App\Services\MediaLibrary\ImageBuilder
     */
    public function makeLink(File $file): ImageBuilder
    {
        return new ImageBuilder($file);
    }


    /**
     * Create media file from uploaded file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param \App\Models\Media\Directory|null $directory
     * @return \App\Models\Media\File
     */
    public function createFileFromUpload(UploadedFile $file, ?Directory $directory = null): File
    {
        $directoryPath = optional($directory)->path ?? '';
        $fileName = $file->getClientOriginalName();
        $path = $directoryPath ? "$directoryPath/$fileName" : $fileName;
        $mimeType = $file->getMimeType();

        $mediaFile = new File([
            'name' => pathinfo($fileName, PATHINFO_FILENAME),
            'extension' => pathinfo($fileName, PATHINFO_EXTENSION),
            'path' => $path,
            'mime_type' => $mimeType,
            'storage' => $this->disk,
            'size' => $file->getSize(),
            'directory_id' => $directory ? $directory->getKey() : null
        ]);

        if ($this->isMimeProcessableFile($mimeType)) {
            $mediaFile->image_resolution = $this->getImageResolution($file->getRealPath());
        }

        $mediaFile->save();
        $this->saveUploadedFile($file, $mediaFile->path);

        return $mediaFile;
    }


    /**
     * Get resolution of the image.
     *
     * @param string $realPath
     * @return null|string
     */
    public function getImageResolution(string $realPath): ?string
    {
        try {
            $image = \Image::make($realPath);
            return "{$image->width()}x{$image->height()}";
        } catch (\Intervention\Image\Exception\NotReadableException $e) {
            // image is not readable, it is not important
            return null;
        }
    }


    /**
     * Create media directory for current storage.
     *
     * @param string $name
     * @param \App\Models\Media\Directory|null $parent
     * @return \App\Models\Media\Directory
     */
    public function createMediaDirectory(string $name, ?Directory $parent): Directory
    {
        return Directory::create([
            'name' => $name,
            'parent_id' => $parent ? $parent->getKey() : null,
            'storage' => $this->disk
        ]);
    }


    /**
     * Check if specified MIME type is processable image.
     *
     * @param string $mimeType
     * @return bool
     */
    public function isMimeProcessableFile(string $mimeType): bool
    {
        return in_array($mimeType, $this->getProcessableMimeTypes());
    }


    /**
     * Get supported image MIME types.
     *
     * @return array
     */
    protected function getProcessableMimeTypes(): array
    {
        if (config('image.driver') === 'imagick') {
            return config('image.imagick_processable_types');
        }

        return config('image.gd_processable_types');
    }


    /**
     * Check if specified MIME type is processable image.
     *
     * @param string $mimeType
     * @return bool
     */
    public function isMimeSelectableImage(string $mimeType): bool
    {
        return in_array($mimeType, $this->getSelectableImageMimeTypes());
    }


    /**
     * Get supported image MIME types.
     *
     * @return array
     */
    public function getSelectableImageMimeTypes(): array
    {
        return config('image.selectable_image_mime_types');
    }


    /**
     * Receive chunk of the file.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\UploadedFile|null
     * @throws \Exception
     */
    public function receiveFile(Request $request): ?UploadedFile
    {
        /** @var \Illuminate\Http\UploadedFile $file */
        if (!$request->hasFile('file')) {
            throw new \Exception("Soubor není součástí tohoto požadavku.");
        }

        if (!$request->has('name') || !$request->has('uuid')) {
            throw new \Exception("Neplatná data požadavku.");
        }

        $path = $this->appendChunk($request);

        if ($request->input('isLast', false) === 'true') {
            return $this->finishFileUpload($path, $request->input('name'));
        }

        return null;
    }


    /**
     * Finish upload of file.
     * Store file to upload directory for finished files.
     *
     * @param string $path - relative path to the temporary file.
     * @param string $finalFileName
     * @return \Illuminate\Http\UploadedFile
     */
    protected function finishFileUpload(string $path, string $finalFileName): UploadedFile
    {
        $donePath = $this->getTempPath(self::DONE_DIRECTORY . '/' . basename($path));

        $this->getLocalStorage()->move($path, $donePath);

        $this->editUploadedFileBeforeStoring($donePath, $finalFileName);

        $realPath = $this->getLocalStorage()->path($donePath);
        $mimeType = $this->getLocalStorage()->mimeType($donePath);

        // Optimize image
        $this->imageOptimizer->optimize($realPath, $mimeType);

        // Create new UploadedFile instance
        return new UploadedFile(
            $realPath, $finalFileName, $mimeType,
            $this->getLocalStorage()->size($donePath),
            UPLOAD_ERR_OK, true
        );
    }


    /**
     * Edit uploaded file before moving to storage.
     *
     * @param string $path
     */
    protected function editUploadedFileBeforeStoring(string $path, string $finalFileName): void
    {
        $path = $this->getLocalStorage()->path($path);
        $ext = pathinfo($finalFileName, PATHINFO_EXTENSION);

        if ($ext === 'svg') {
            $filePointer = fopen($path, 'r');
            $data = fread($filePointer, 5);
            fclose($filePointer);

            if ($data !== '<?xml') {
                $content = file_get_contents($path);
                $content = '<?xml version="1.0" encoding="utf-8"?>' . $content;
                file_put_contents($path, $content);
            }
        }
    }


    /**
     * Append chunk to the file.
     *
     * @param \Illuminate\Http\Request $request
     * @return string - relative path to the file
     */
    protected function appendChunk(Request $request): string
    {
        /** @var \Illuminate\Http\UploadedFile $file */
        $file = $request->file('file');
        $requestKey = $this->getRequestKey($request);
        $path = \Cache::get($requestKey);

        if (!$path) {
            $filename = str_random(64);
            $path = $this->getTempPath($filename);
            $this->getLocalStorage()->put($path, '');
            \Cache::put($requestKey, $path, self::MAX_TEMP_FILE_AGE / 60);
        }

        if (!$out = fopen($this->getLocalStorage()->path($path), 'ab')) {
            throw new UploadException('Nelze otevřít začátek nahrávaného souboru.');
        }

        if (!$in = @fopen($file->getRealPath(), 'rb')) {
            throw new UploadException('Nelze otevřít vstupní soubor.');
        }

        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }

        @fclose($out);
        @fclose($in);

        return $path;
    }


    /**
     * Cancel upload - remove temporary files.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function cancelUpload(Request $request): bool
    {
        $requestKey = $this->getRequestKey($request);
        $path = \Cache::get($requestKey);

        if (!$path) {
            return false;
        }

        \Cache::forget($requestKey);
        return $this->getLocalStorage()->delete($path);
    }


    /**
     * Get cache key for current upload.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    protected function getRequestKey(Request $request): string
    {
        return md5(join('-', [
            $request->getClientIp(),
            $request->userAgent(),
            auth()->check() ? auth()->id() : 0,
            $request->input('name'),
            $request->input('uuid'),
        ]));
    }


    /**
     * Get path to temp directory on local storage.
     *
     * @param string $filename
     * @return string
     */
    protected function getTempPath(string $filename): string
    {
        return self::ROOT_DIRECTORY . '/' . self::TEMP_DIRECTORY . '/' . $filename;
    }


    /**
     * Get path to cache directory on local storage.
     *
     * @param string $filename
     * @return string
     */
    protected function getCachePath(string $filename): string
    {
        return self::ROOT_DIRECTORY . '/' . self::CACHE_DIRECTORY . '/' . $filename;
    }


    /**
     * Get storage for temporary files.
     *
     * @return \Illuminate\Filesystem\FilesystemAdapter
     */
    protected function getLocalStorage(): FilesystemAdapter
    {
        if ($this->isLocalDisk()) {
            return $this->storage;
        }

        return $this->getLocalDisk();
    }


    /**
     * Is using local disk?
     *
     * @return bool
     */
    protected function isLocalDisk(): bool
    {
        $driver = config("filesystems.disks.{$this->disk}.driver");
        return $driver === 'local';
    }


    /**
     * Get local storage.
     *
     * @return \Illuminate\Filesystem\FilesystemAdapter
     */
    protected function getLocalDisk(): FilesystemAdapter
    {
        return \Storage::disk('local');
    }


    /**
     * Get storage.
     *
     * @return \Illuminate\Filesystem\FilesystemAdapter
     */
    public function getStorage(): FilesystemAdapter
    {
        return $this->storage;
    }


    /**
     * Add root directory prefix to the path.
     *
     * @param string $path
     * @return string
     */
    public function getStoragePath(string $path): string
    {
        return self::ROOT_DIRECTORY . "/$path";
    }


    /**
     * Create directory.
     *
     * @param string $dirname
     * @return bool - TRUE on success, FALSE on failure
     */
    public function createDir(string $dirname): bool
    {
        return $this->storage->createDir($this->getStoragePath($dirname));
    }


    /**
     * Delete directory.
     *
     * @param string $dirname
     * @return bool - TRUE on success, FALSE on failure
     */
    public function deleteDir(string $dirname): bool
    {
        return $this->storage->deleteDirectory($this->getStoragePath($dirname));
    }


    /**
     * Rename directory.
     *
     * @param string $from
     * @param string $to
     * @return bool - TRUE on success, FALSE on failure
     */
    public function renameDir(string $from, string $to): bool
    {
        return $this->storage->move($this->getStoragePath($from), $this->getStoragePath($to));
    }


    /**
     * Move file.
     *
     * @param string $from
     * @param string $to
     * @return bool - TRUE on success, FALSE on failure
     */
    public function moveFile(string $from, string $to): bool
    {
        FileCache::forFile($from)->moveCache($to);
        return $this->storage->move($this->getStoragePath($from), $this->getStoragePath($to));
    }


    /**
     * Delete file.
     *
     * @param string $path - short path
     * @return bool - TRUE on success, FALSE on failure
     */
    public function deleteFile(string $path): bool
    {
        $this->clearFileCache($path);
        return $this->storage->delete($this->getStoragePath($path));
    }


    /**
     * Check if file exists.
     *
     * @param string $path
     * @return bool
     */
    public function fileExists(string $path): bool
    {
        return $this->storage->exists($this->getStoragePath($path));
    }


    /**
     * Create a streamed response for a given file.
     *
     * @param string $path - short path
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function toResponse(string $path): StreamedResponse
    {
        return $this->storage->response($this->getStoragePath($path));
    }


    /**
     * Get intervention image instance for specified file.
     * File must be supported image!
     *
     * @param string $path - short path
     * @return \App\Services\MediaLibrary\AbstractProcessableFile
     */
    public function getProcessableFile(string $path): AbstractProcessableFile
    {
        return AbstractProcessableFile::makeFromPath($this->storage->path($this->getStoragePath($path)));
    }


    /**
     * Get cached file under specified cache key.
     *
     * @param string $cacheFile
     * @return \App\Services\MediaLibrary\AbstractProcessableFile|null
     */
    public function getCachedFile(string $cacheFile): ?AbstractProcessableFile
    {
        return AbstractProcessableFile::makeFromPath($this->getLocalStorage()->path($this->getCachePath($cacheFile)));
    }


    /**
     * Store image to cache.
     *
     * @param string $cacheFile
     * @param \App\Services\MediaLibrary\AbstractProcessableFile $file
     */
    public function storeFileToCache(string $cacheFile, AbstractProcessableFile $file): void
    {
        $cachePath = $this->getCachePath($cacheFile);
        $cachedFilePath = $this->getLocalStorage()->path($cachePath);

        $this->getLocalStorage()->put($cachePath, $file->getContent());
        $this->imageOptimizer->optimize($cachedFilePath, $file->getMimeType());
    }


    /**
     * Get the file's last modification time.
     *
     * @param string $path
     * @return \DateTime
     */
    public function lastModified(string $path): \DateTime
    {
        $timestamp = $this->storage->lastModified($this->getStoragePath($path));
        return (new \DateTime())->setTimestamp($timestamp);
    }


    /**
     * Import specified file into media library.
     *
     * @param string $path
     * @param string $name
     * @return \App\Models\Media\File
     * @throws \Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException
     * @throws \Exception
     */
    public function importFile(string $path, string $name): File
    {
        $file = new \Illuminate\Http\File($path);
        $mimeType = $file->getMimeType();

        // Optimize image
        $this->imageOptimizer->optimize($path, $mimeType);

        $mediaFile = new File([
            'name' => $name,
            'extension' => pathinfo($path, PATHINFO_EXTENSION),
            'mime_type' => $mimeType,
            'storage' => $this->disk,
            'size' => $file->getSize()
        ]);

        if ($this->isMimeProcessableFile($mimeType)) {
            $mediaFile->image_resolution = $this->getImageResolution($path);
        }

        $mediaFile->save();

        try {
            $this->getStorage()->putFileAs(
                $this->getStoragePath(dirname($mediaFile->path)), $file, basename($mediaFile->path)
            );
        } catch (\Exception $e) {
            $mediaFile->delete();
            return null;
        }

        return $mediaFile;
    }


    /**
     * Clear cache file under specified key.
     *
     * @param string $cacheFile
     */
    public function removeCacheFile(string $cacheFile): void
    {
        $cachePath = $this->getCachePath($cacheFile);
        $this->getLocalStorage()->delete($cachePath);
    }


    /**
     * Move cache file under given key.
     *
     * @param string $from
     * @param string $to
     * @return bool
     */
    public function moveCacheFile(string $from, string $to): bool
    {
        try {
            $this->getLocalStorage()->rename($this->getCachePath($from), $this->getCachePath($to));
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}
