{{ Form::model($configuration, ['id' => 'model-articleslist-configuration-form']) }}

<div class="alert alert-warning" v-if="!hasViews">
    {{ trans('module-articleslist::admin.grid_editor_form.no_views') }}
</div>

<template v-else>
    {{-- View --}}
    <v-form-group :required="true" :error="form.getError('view')">
        <label for="mv-articleslist-input-view">
            {{ trans('module-articleslist::admin.grid_editor_form.labels.view') }}
        </label>
        {{ Form::select('view', $views, null, [
            'class' => 'form-control',
            'id' => 'mv-articleslist-input-view',
            'v-model' => 'form.view'
        ]) }}
    </v-form-group>

    {{-- Categories --}}
    <v-form-group :error="form.getError('category_ids')">
        <label for="mv-articleslist-input-category-ids">
            {{ trans('module-articleslist::admin.grid_editor_form.labels.category_ids') }}
        </label>
        <multiselect name="category_ids[]"
                     v-model="form.category_ids"
                     :options="{{ json_encode($categories) }}"
                     :multiple="true"
                     label="label"
                     track-by="id"
                     group-label="label"
                     group-values="children"
        ></multiselect>
    </v-form-group>

    {{-- Tags --}}
    <v-form-group :error="form.getError('tag_ids')">
        <label for="mv-articleslist-input-tag-ids">
            {{ trans('module-articleslist::admin.grid_editor_form.labels.tag_ids') }}
        </label>
        <multiselect name="tag_ids[]"
                     v-model="form.tag_ids"
                     :options="{{ json_encode($tags) }}"
                     :multiple="true"
                     label="name"
                     track-by="id"
        >
        </multiselect>
    </v-form-group>

    {{-- Sort types --}}
    <v-form-group :required="true" :error="form.getError('sort_type')">
        <label for="mv-articleslist-input-sort-type">
            {{ trans('module-articleslist::admin.grid_editor_form.labels.sort_type') }}
        </label>
        {{ Form::select('sort_type', $sortTypes, null, [
            'class' => 'form-control',
            'id' => 'mv-articleslist-input-sort-type',
            'v-model' => 'form.sort_type'
        ]) }}
    </v-form-group>

    {{-- Limit --}}
    <v-form-group :error="form.getError('limit')">
        <label for="mv-articleslist-input-limit">
            {{ trans('module-articleslist::admin.grid_editor_form.labels.limit') }}
        </label>
        {{ Form::number('limit', null, [
            'class' => 'form-control',
            'id' => 'mv-articleslist-input-limit',
            'v-model' => 'form.limit',
            'min' => 0
        ]) }}
    </v-form-group>
</template>

{{ Form::close() }}

<script>
    window.articlesListModuleOptions = function () {
        return {!! json_encode([
            'model' => $configuration->only(['view', 'category_ids', 'tag_ids', 'sort_type', 'limit']),
            'views' => $views,
            'categories' => $categories,
            'tags' => $tags,
            'sortTypes' => $sortTypes,
            'trans' => trans('module-articleslist::admin.grid_editor_form')
        ]) !!};
    };
</script>

@include('admin.vendor.form._scripts')
