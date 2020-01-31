{{-- Nadpis článku --}}
<v-form-group :required="true" :error="form.getError('title')">
    {!! Form::label('title', trans("admin/article/form.labels.title")) !!}
    {!! Form::text('title', null, [
        'class' => 'form-control',
        'maxlength' => '250',
        ':value' => 'form.title',
        '@change' => 'onTitleChanged',
        'v-maxlength'
    ]) !!}
</v-form-group>

{{-- URL slug článku --}}
<v-form-group :required="true" :error="form.getError('url')">
    {!! Form::label('url', trans("admin/article/form.labels.url")) !!}
    {!! Form::text('url', null, [
        'class' => 'form-control',
        'maxlength' => '250',
        ':value' => 'form.url',
        '@change' => 'onUrlChanged',
        'v-maxlength'
    ]) !!}
</v-form-group>

{{-- Tagy článku - volitelná funkcionalita --}}
@if ($flag->use_tags)
    <v-form-group :required="true" :error="form.getError('tags')">
        {!! Form::label('tags', trans("admin/article/form.labels.tags")) !!}
        {!! Form::text('tags', $article->tags()->pluck('name')->implode(','), [
            'class' => 'form-control',
            'id' => 'input-tags'
        ]) !!}
    </v-form-group>
@endif

{{-- Perex článku --}}
<v-form-group :required="true" :error="form.getError('perex')">
    {!! Form::label('perex', trans("admin/article/form.labels.perex")) !!}
    {!! Form::textarea('perex', null, [
        'class' => 'form-control noresize medium',
        'v-model' => 'form.perex'
    ]) !!}
</v-form-group>

@if (!$flag->use_grid_editor)
    {{-- Text článku --}}
    <v-form-group :error="form.getError('text')">
        {!! Form::label('text', trans("admin/article/form.labels.text")) !!}
        {!! Form::textarea('text', null, [
            'class' => 'form-control',
            'ref' => 'inputText',
            ':value' => 'form.text',
            'rows' => 10
        ]) !!}
    </v-form-group>
@endif

@if ($canChangeUser)
    {{-- Autor článku --}}
    <v-form-group :required="true" :error="form.getError('user_id')">
        {!! Form::label('user_id', trans("admin/article/form.labels.user_id")) !!}
        {!! Form::select('user_id', $users, null, [
            'class' => 'form-control',
            'v-model' => 'form.user_id'
        ]) !!}
    </v-form-group>
@endif

{{-- Obrázek článku --}}
<v-form-group :has-error="form.hasError('image_id')">
    {!! Form::label('image_id', trans("admin/article/form.labels.image_id")) !!}

    <media-library-file-selector :image="true"
                                 name="image_id"
                                 :error="form.getError('image_id')"
                                 v-model="form.image_id"
    ></media-library-file-selector>
</v-form-group>

{{-- Video článku --}}
<v-form-group :has-error="form.hasError('video_id')">
    {!! Form::label('video_id', trans("admin/article/form.labels.video_id")) !!}

    <media-library-file-selector :image="false"
                                 :video="true"
                                 name="video_id"
                                 :error="form.getError('video_id')"
                                 v-model="form.video_id"
    ></media-library-file-selector>
</v-form-group>
