<?php /** @var \App\Models\Web\Theme $theme */ ?>

<div class="col-xs-12">
    <v-form-group :error="form.getError('{{ $name = $theme->getSettingsKey("{$language_code}_articles_page_id") }}')">
        <label for="input-theme-articles-page-id">{{ trans('theme::config.form_labels.articles_page_id') }}</label>
        {{ Form::select($name, $pages, $articlesPageId, [
            'class' => 'form-control',
            'v-model' => "form.$name"
        ]) }}
    </v-form-group>
</div>