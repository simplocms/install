<?php
$cache = resolve('cache');
$sortBy = $cache->get(\App\Http\Controllers\Admin\MediaLibraryController::MEDIA_LIBRARY_SORT_CACHE_KEY);
$sortDir = $cache->get(\App\Http\Controllers\Admin\MediaLibraryController::MEDIA_LIBRARY_SORT_DIR_CACHE_KEY);
?>

@extends('admin.layouts.master')

@section('content')
    <media-library :trans="{{ json_encode(trans('admin/media_library')) }}"
                   :warn-cache-driver="{!! config('cache.default') === 'array' ? 'true' : 'false' !!}"
                   sort-by="{{ $sortBy }}"
                   sort-dir="{{ $sortDir }}"
    ></media-library>
@endsection

@push('style')
    <style>
        .page-header {
            display: none;
        }

        .content {
            padding: 0 0 20px;
        }
        .footer {
            padding: 0 20px;
            bottom: 10px;
        }
    </style>
@endpush
