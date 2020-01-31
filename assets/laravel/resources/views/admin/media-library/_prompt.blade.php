<?php
$cache = resolve('cache');
$sortBy = $cache->get(\App\Http\Controllers\Admin\MediaLibraryController::MEDIA_LIBRARY_SORT_CACHE_KEY);
$sortDir = $cache->get(\App\Http\Controllers\Admin\MediaLibraryController::MEDIA_LIBRARY_SORT_DIR_CACHE_KEY);
?>

<media-library-prompt :trans="{{ json_encode(trans('admin/media_library')) }}"
                      :warn-cache-driver="{!! config('cache.default') === 'array' ? 'true' : 'false' !!}"
                      sort-by="{{ $sortBy }}"
                      sort-dir="{{ $sortDir }}"
></media-library-prompt>

@push('script')
    <script>
        window.mediaLibraryUrls = function () {
            return {!! json_encode([
                'directoryTree' => route('admin.media.tree'),
                'directoryContent' => route('admin.media.directories.contents'),
                'createDirectory' => route('admin.media.directories.create'),
                'updateDirectory' => route('admin.media.directories.update', '%_i_%'),
                'deleteDirectory' => route('admin.media.directories.delete', '%_i_%'),
                'upload' => route('admin.media.directories.upload'),
                'deleteFiles' => route('admin.media.files.delete', '%_a_%'),
                'updateFile' => route('admin.media.files.update', '%_i_%'),
                'overrideFile' => route('admin.media.files.override', '%_i_%'),
                'search' => route('admin.media.search'),
            ]) !!}
        }
    </script>
@endpush
