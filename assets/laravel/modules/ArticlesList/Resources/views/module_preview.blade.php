<?php
/** @var \Modules\ArticlesList\Models\Configuration $configuration */
$viewName = \App\Helpers\ViewHelper::getViewName('modules.articles_list', $configuration->view);
$categories = \App\Models\Article\Category::whereIn('id', $configuration->category_ids)->get()->implode('name', ', ');
$tags = \App\Models\Article\Tag::whereIn('id', $configuration->tag_ids)->get()->implode('name', ', ');
$sortType = \Modules\ArticlesList\Models\SortTypeEnum::labels()[$configuration->sort_type];
$limit = trans_choice('module-articleslist::admin.preview.limit', $configuration->limit, [
    'count' => $configuration->limit
]);
?>
<strong>{{ trans('module-articleslist::admin.preview.labels.view') }}:</strong>
@if ($viewName)
    {{ $viewName }}
@else
    <span class="text-danger">{{ trans('module-articleslist::admin.preview.not_found') }}</span>
@endif
<br>
<strong>{{ trans('module-articleslist::admin.preview.labels.limit') }}:</strong> {{ $limit }}<br>
@if ($configuration->category_ids)
    <strong>{{ trans('module-articleslist::admin.preview.labels.category_ids') }}:</strong> {{ $categories }}<br>
@endif
@if ($configuration->tag_ids)
    <strong>{{ trans('module-articleslist::admin.preview.labels.tag_ids') }}:</strong> {{ $tags }}<br>
@endif
<strong>{{ trans('module-articleslist::admin.preview.labels.sort_type') }}:</strong> {{ $sortType }}<br>


