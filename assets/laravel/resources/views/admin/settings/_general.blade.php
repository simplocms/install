<div class="row">
    <fieldset class="content-group col-sm-6">
        <legend class="text-bold pt-5">
            <i class="fa fa-object-group"></i>
            {{ trans('admin/settings.general_box_title') }}
        </legend>

        {{-- Site name --}}
        <v-form-group :error="form.getError('site_name')">
            {!! Form::label('input-site-name', trans("admin/settings.labels.site_name")) !!}
            <input maxlength="65"
                   name="site_name"
                   id="input-site-name"
                   class="form-control"
                   v-model="form.site_name"
                   v-maxlength
            >
        </v-form-group>

        {{-- Company name --}}
        <v-form-group :error="form.getError('company_name')">
            {!! Form::label('input-company-name', trans("admin/settings.labels.company_name")) !!}
            <input maxlength="100"
                   name="company_name"
                   id="input-company-name"
                   class="form-control"
                   v-model="form.company_name"
                   :placeholder="form.site_name"
                   v-maxlength
            >
        </v-form-group>

        {{-- Logo --}}
        <v-form-group :has-error="form.getError('logo')">
            <label>{{ trans('admin/settings.labels.logo') }}</label>
            <span class="help-block pb-10">{!! trans('admin/settings.general.logo_help') !!}</span>
            <media-library-file-selector :image="true"
                                         name="logo"
                                         :error="form.getError('logo')"
                                         v-model="form.logo"
            ></media-library-file-selector>
        </v-form-group>

        {{-- Favicon --}}
        <v-form-group :has-error="form.getError('favicon')">
            <label>{{ trans('admin/settings.labels.favicon') }}</label>
            <span class="help-block pb-10">{!! trans('admin/settings.general.favicon_help') !!}</span>
            <media-library-file-selector :image="true"
                                         name="favicon"
                                         :error="form.getError('favicon')"
                                         v-model="form.favicon"
            ></media-library-file-selector>
        </v-form-group>

        {{-- Theme color --}}
        <v-form-group :has-error="form.getError('theme_color')">
            <label>{{ trans('admin/settings.labels.theme_color') }}</label>
            <input name="theme_color"
                   id="input-theme-color"
                   v-model="form.theme_color"
                   class="form-control"
                   type="color"
            >
        </v-form-group>
    </fieldset>

    <fieldset class="content-group col-sm-6 form-horizontal">
        <legend class="text-bold pt-5">
            <i class="fa fa-object-group"></i>
            {{ trans('admin/settings.theme_box_title') }}
        </legend>

        <v-form-group class="col-xs-12">
            {{ trans('admin/settings.labels.active_theme') }}: <strong>{{ $defaultTheme->name }}</strong>
            <button type="button" class="btn btn-warning btn-xs pull-right" @click.prevent="openThemeModal">
                {{ trans('admin/settings.btn_change_theme') }}
            </button>
        </v-form-group>

        @include('admin.settings._theme')
    </fieldset>
</div>
