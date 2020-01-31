<?php
$ckConfig = \App\Structures\Enums\SingletonEnum::theme()->getCKEditorConfig();
?>

<script>
window.onAppReady = function () {
    @include('admin.vendor.flash.message')
    Request.ping("{{ route('admin.ping') }}", {{ $pingInterval }});
};
window.cms_locale = '{{ app()->getLocale() }}';
window.cms_trans = {!! json_encode([
    'notifications' => trans('admin/general.notifications'),
    'flash_level' => trans('admin/general.flash_level'),
    'update_browser' => trans('general.update_browser'),
    'file_selector' => trans('admin/media_library.file_selector'),
    'components' => trans('admin/js_components'),
]) !!}

@if ($ckConfig)
window.ck_config = {!! json_encode($ckConfig) !!};
@endif
</script>
