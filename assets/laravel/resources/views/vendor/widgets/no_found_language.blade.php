<div style="color: #a94442;background-color: #f2dede;padding: 15px;margin: 5px;border: 1px solid #ebccd1;">
    @if ($languageName)
        {{ trans('admin/widgets/general.no_language_fallback.known_language', [
            'id' => $id,
            'language' => $languageName
        ]) }}
    @else
        {{ trans('admin/widgets/general.no_language_fallback.unknown_language', ['id' => $id]) }}
    @endif
</div>
