<?php
/** @var \App\Models\Web\Language $contentLanguage */
?>
{{-- Search enable --}}
<v-form-group :error="form.getError('search_enabled')">
    <v-checkbox-switch v-model="form.search_enabled">
        {{ trans('admin/settings.search.search_enabled') }}
    </v-checkbox-switch>
</v-form-group>

<template v-if="form.search_enabled">
    {{-- URL slug --}}
    <v-form-group :error="form.getError('search_uri')">
        <label for="input-search-uri">{{ trans('admin/settings.search.search_uri') }}</label>
        <span class="help-block">
        {!! trans('admin/settings.search.search_uri_info', [
            'language' => $contentLanguage->name,
            'url' => UrlFactory::getHomepageUrl($contentLanguage) . "/@{{ form.search_uri }}"
        ]) !!}
    </span>
        <input maxlength="100"
               name="search_uri"
               type="text"
               id="input-search-uri"
               @change="form.search_uri = $event.target.value"
               :value="form.search_uri"
               class="form-control"
               v-maxlength
        >
    </v-form-group>

    {{-- Search in pages --}}
    <v-form-group :error="form.getError('search_in_pages')">
        <v-checkbox-switch v-model="form.search_in_pages">
            {{ trans('admin/settings.search.search_in_pages') }}
        </v-checkbox-switch>
    </v-form-group>

    {{-- Search in artichles --}}
    <v-form-group :error="form.getError('search_in_articles')">
        <v-checkbox-switch v-model="form.search_in_articles">
            {{ trans('admin/settings.search.search_in_articles') }}
        </v-checkbox-switch>
    </v-form-group>

    {{-- Search in categories --}}
    <v-form-group :error="form.getError('search_in_categories')">
        <v-checkbox-switch v-model="form.search_in_categories">
            {{ trans('admin/settings.search.search_in_categories') }}
        </v-checkbox-switch>
    </v-form-group>

    {{-- Search in photogalleries --}}
    <v-form-group :error="form.getError('search_in_photogalleries')">
        <v-checkbox-switch v-model="form.search_in_photogalleries">
            {{ trans('admin/settings.search.search_in_photogalleries') }}
        </v-checkbox-switch>
    </v-form-group>
</template>
