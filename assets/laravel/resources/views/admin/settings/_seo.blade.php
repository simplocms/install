{{-- SEO title --}}
<v-form-group :error="form.getError('seo_title')">
    {!! Form::label('input-title', trans("admin/settings.labels.seo_title")) !!}
    <span class="help-block pb-10">{!! trans('admin/settings.general.title_help') !!}</span>
    <input maxlength="65"
           name="seo_title"
           id="input-seo-title"
           class="form-control"
           v-model="form.seo_title"
           v-maxlength
    >
</v-form-group>

{{-- SEO description --}}
<v-form-group :error="form.getError('seo_description')">
    {!! Form::label('input-seo-description', trans("admin/settings.labels.seo_description")) !!}
    <textarea maxlength="320"
              name="seo_description"
              id="input-seo-description"
              class="form-control"
              v-model="form.seo_description"
              rows="3"
              v-maxlength
    ></textarea>
</v-form-group>
