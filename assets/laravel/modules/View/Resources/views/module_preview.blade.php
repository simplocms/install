<?php
/** @var \Modules\View\Models\Configuration $configuration */
$viewName = \App\Helpers\ViewHelper::getViewName('modules.view', $configuration->view);
$fields = \App\Helpers\ViewHelper::getViewVariables($configuration->view);
$variables = $configuration->getInitializedVariables();
?>
<strong>{{ trans('module-view::admin.preview.title') }}:</strong>
@if ($viewName)
    {{ $viewName }}
@else
    <span class="text-danger">{{ trans('module-view::admin.preview.not_found') }}</span>
@endif
<br>

@if ($configuration->variables)
    @foreach ($fields as $index => $field)
        <br>
        <strong>{{ $field->getLabel() ?? $field->getName() }}:</strong>
        <?php $value = $variables[$field->getName()] ?? 'null'; ?>
        @if ($value instanceof \App\Models\Media\File)
            {{ str_limit($value->getFullName()) }}
        @else
            {{ str_limit($value) }}
        @endif
    @endforeach
@endif
