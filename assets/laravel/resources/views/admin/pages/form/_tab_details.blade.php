{{-- Název stránky --}}
<v-form-group :required="true" :error="form.getError('name')">
    {!! Form::label('name', trans("admin/pages/form.labels.name")) !!}
    {!! Form::text('name', null, [
        'class' => 'form-control maxlength',
        'maxlength' => '150',
        ':value' => 'form.name',
        '@change' => 'onNameChanged'
    ]) !!}
</v-form-group>

{{-- URL slug stránky --}}
<v-form-group :required="!form.is_homepage" :error="form.getError('url')" v-if="!isTestingCounterpart">
    {!! Form::label('url', trans("admin/pages/form.labels.url")) !!}
    {!! Form::text('url', null, [
        'class' => 'form-control maxlength',
        'maxlength' => '100',
        ':value' => 'form.url',
        '@change' => 'onUrlChanged',
        ':disabled' => 'form.is_homepage'
    ]) !!}
</v-form-group>

{{-- View stránky --}}
<v-form-group :error="form.getError('view')">
    {!! Form::label('view', trans("admin/pages/form.labels.view")) !!}

    <multiselect name="view"
                 v-model="form.view"
                 :options="{{ json_encode($views) }}"
                 label="label"
                 track-by="key"
                 group-label="label"
                 group-values="children"
                 placeholder="{{ trans('admin/pages/form.placeholders.view') }}"
    ></multiselect>
</v-form-group>

{{-- Nadřazená stránka --}}
<v-form-group :error="form.getError('parent_id')" v-if="!isTestingCounterpart">
    {!! Form::label('input-parent-id', trans("admin/pages/form.labels.parent_id")) !!}

    <multiselect name="parent_id"
                 v-model="form.parent_id"
                 :options="{{ json_encode($parentPages) }}"
                 label="name"
                 track-by="id"
                 placeholder="{{ trans('admin/pages/form.placeholders.parent_id') }}"
    >
        <template slot="option" slot-scope="props">
            <span v-if="props.option.depth > 0" :style="{'padding-left': 10 * props.option.depth }">⤷</span>
            @{{ props.option.name }}
        </template>
    </multiselect>

</v-form-group>

{{-- Obrázek stránky --}}
<v-form-group :has-error="form.getError('image_id')">
    <media-library-file-selector :image="true"
                                 name="image_id"
                                 :error="form.getError('image_id')"
                                 v-model="form.image_id"
    ></media-library-file-selector>
</v-form-group>

{{-- Je stránka úvodní? --}}
<v-checkbox-switch v-model="form.is_homepage" name="is_homepage" v-if="!isTestingCounterpart">
    {{ trans('admin/pages/form.labels.is_homepage') }}
</v-checkbox-switch>

{{-- Publikovat stránku? --}}
<v-checkbox-switch v-model="form.published" name="published" v-if="!isTestingCounterpart">
    {{ trans('admin/pages/form.labels.published') }}
</v-checkbox-switch>
