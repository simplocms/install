<strong>{{ trans('module-image::admin.grideditor_title') }}</strong><br/>
<a href="{{ $configuration->makeImageLink('image')->getUrl() }}" alt="{{ $configuration->alt }}" target="_blank">
    {{ $configuration->alt }}
    @if ($configuration->is_sized)
        ({{ $configuration->width }}x{{ $configuration->height }} px)
    @endif
</a>
