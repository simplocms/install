<div class="tabbable tab-content-bordered">
    <ul class="nav nav-tabs nav-tabs-highlight">
        <li class="active">
            <a href="#tab_details" data-toggle="tab" aria-expanded="true">
                {{ trans('admin/article_flags/form.tabs.details') }}
            </a>
        </li>
        <li>
            <a href="#tab_seo" data-toggle="tab" aria-expanded="false">
                {{ trans('admin/article_flags/form.tabs.seo') }}
            </a>
        </li>
        <li>
            <a href="#tab_og" data-toggle="tab" aria-expanded="false">
                {{ trans('admin/article_flags/form.tabs.og') }}
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane has-padding active" id="tab_details">
            {{-- Flag name --}}
            <v-form-group :required="true" :error="form.getError('name')">
                {!! Form::label('name', trans("admin/article_flags/form.labels.name")) !!}
                {!! Form::text('name', null, [
                    'class' => 'form-control maxlength',
                    'maxlength' => '50',
                    ':value' => 'form.name',
                    '@change' => 'onNameChanged'
                ]) !!}
            </v-form-group>

            {{-- Flag URL slug --}}
            <v-form-group :required="true" :error="form.getError('url')">
                {!! Form::label('url', trans("admin/article_flags/form.labels.url")) !!}
                {!! Form::text('url', null, [
                    'class' => 'form-control maxlength',
                    'maxlength' => '50',
                    ':value' => 'form.url',
                    '@change' => 'onUrlChanged'
                ]) !!}
            </v-form-group>

            {{-- Flag description --}}
            <v-form-group :error="form.getError('description')">
                {!! Form::label('description', trans("admin/article_flags/form.labels.description")) !!}
                {!! Form::textarea('description', null, [
                    'class' => 'form-control',
                    'maxlength' => '1000',
                    'v-model' => 'form.description',
                    'v-maxlength',
                    'rows' => 6
                ]) !!}
            </v-form-group>

            {{-- Use tags? --}}
            <v-checkbox-switch v-model="form.use_tags" name="use_tags">
                {{ trans("admin/article_flags/form.labels.use_tags") }}
            </v-checkbox-switch>

            {{-- Use Grid Editor? --}}
            <v-checkbox-switch v-model="form.use_grid_editor" name="use_grid_editor">
                {{ trans("admin/article_flags/form.labels.use_grid_editor") }}
            </v-checkbox-switch>

            {{-- Use Grid Editor? --}}
            <v-checkbox-switch v-model="form.should_bound_articles_to_category"
                               name="should_bound_articles_to_category"
            >
                {{ trans("admin/article_flags/form.labels.should_bound_articles_to_category") }}
            </v-checkbox-switch>
        </div>

        <div class="tab-pane has-padding" id="tab_seo">
            <seo-inputs :title-placeholder="form.name"
                        :form="form"
                        :trans="{{ \App\Helpers\Functions::combineTransToJson([
                            'admin/general.seo', 'admin/article_flags/form.seo_tab'
                        ]) }}"
            ></seo-inputs>
        </div>

        <div class="tab-pane has-padding" id="tab_og">
            <open-graph-inputs :title-placeholder="form.name"
                               :url-placeholder="form.url"
                               :form="form"
                               :trans="{{ json_encode(trans('admin/general.open_graph')) }}"
            ></open-graph-inputs>
        </div>
    </div>
</div>
