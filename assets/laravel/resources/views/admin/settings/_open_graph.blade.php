<h4>{{ trans('admin/settings.twitter_title') }}</h4>

{{-- Twitter account --}}
<v-form-group :error="form.getError('twitter_account')">
    <label for="input-twitter-account">{{ trans('admin/settings.labels.twitter_account') }}</label>
    <span class="help-block pb-10">{!! trans('admin/general.twitter.help_text') !!}</span>
    <input maxlength="50"
           name="twitter_account"
           type="text"
           id="input-twitter-account"
           v-model="form.twitter_account"
           class="form-control"
           placeholder="@"
           v-maxlength
    >
</v-form-group>

<h4>{{ trans('admin/settings.open_graph_title') }}</h4>

{{-- OG title --}}
<v-form-group :error="form.getError('og_title')">
    <label for="input-og-title">{{ trans('admin/general.open_graph.inputs.title.label') }}</label>
    <span class="help-block pb-10">{!! trans('admin/settings.general.title_help') !!}</span>
    <input maxlength="90"
           name="og_title"
           type="text"
           id="input-og-title"
           v-model="form.og_title"
           class="form-control"
           v-maxlength
    >
</v-form-group>

{{-- OG description --}}
<v-form-group :error="form.getError('og_description')">
    <label for="input-og-description">{{ trans('admin/general.open_graph.inputs.description.label') }}</label>
    <textarea maxlength="300"
              name="og_description"
              id="input-og-description"
              class="form-control"
              v-model="form.og_description"
              rows="3"
              v-maxlength
    ></textarea>
</v-form-group>

{{-- OG image --}}
<v-form-group :has-error="form.getError('og_image')">
    <label>{{ trans('admin/general.open_graph.inputs.image.label') }}</label>
    <media-library-file-selector :image="true"
                                 name="og_image"
                                 :error="form.getError('og_image')"
                                 v-model="form.og_image"
    ></media-library-file-selector>
</v-form-group>
