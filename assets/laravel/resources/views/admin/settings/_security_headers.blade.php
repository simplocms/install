<div class="row">
    <div class="col-sm-6">
        {{-- X-Frame-Options --}}
        <v-form-group :error="form.getError('x_frame_options')">
            <label for="input-x-frame-options">X-Frame-Options</label>
            <span class="help-block">{!! trans('admin/settings.security_headers.x_frame_options_info') !!}</span>
            <input name="x_frame_options"
                   type="text"
                   id="input-x-frame-options"
                   v-model="form.x_frame_options"
                   class="form-control"
                   list="x-frame-options-list"
            >
            <datalist id="x-frame-options-list">
                <option value="sameorigin"></option>
                <option value="deny"></option>
            </datalist>
        </v-form-group>

        {{-- X-Xss-Protection --}}
        <v-form-group :error="form.getError('x_xss_protection')">
            <label for="input-x-xss-protection">X-Xss-Protection</label>
            <span class="help-block">{!! trans('admin/settings.security_headers.x_xss_protection_info') !!}</span>
            {{ Form::select('x_xss_protection', $xssProtectionOptions, null, [
                'v-model' => 'form.x_xss_protection',
                'id' => 'input-x-xss-protection',
                'class' => 'form-control'
            ]) }}
        </v-form-group>

        {{-- Referrer policy --}}
        <v-form-group :error="form.getError('referrer_policy')">
            <label for="input-referrer-policy">Referrer-Policy</label>
            <span class="help-block">{!! trans('admin/settings.security_headers.referrer_policy_info') !!}</span>
            {{ Form::select('referrer_policy', $referrerPolicyOptions, null, [
                'v-model' => 'form.referrer_policy',
                'id' => 'input-referrer-policy',
                'class' => 'form-control'
            ]) }}
        </v-form-group>

        {{-- X-Content-Type-Options --}}
        <v-form-group :error="form.getError('x_content_type_options')">
            <label>X-Content-Type-Options: nosniff</label>
            <span class="help-block">
                {!! trans('admin/settings.security_headers.x_content_type_options_info') !!}
            </span>
            <v-checkbox-switch v-model="form.x_content_type_options">
                {{ trans('admin/settings.security_headers.enable') }}
            </v-checkbox-switch>
        </v-form-group>
    </div>

    {{-- HTTP Strict Transport Security --}}
    <fieldset class="content-group col-sm-6">
        <legend class="text-bold pt-5 mb-5">
            HTTP Strict Transport Security
        </legend>

        <span class="help-block pb-10">{!! trans('admin/settings.security_headers.hsts_info') !!}</span>

        <v-form-group :error="form.getError('hsts_enabled')">
            <v-checkbox-switch v-model="form.hsts_enabled">
                {{ trans('admin/settings.security_headers.enable') }}
            </v-checkbox-switch>
        </v-form-group>

        <div v-if="form.hsts_enabled">
            <v-form-group :error="form.getError('hsts_max_age')">
                <label for="input-hsts-max-age">Max-age</label>
                <input name="hsts_max_age"
                       type="number"
                       id="input-x-hsts-max-age"
                       v-model.number="form.hsts_max_age"
                       class="form-control"
                >
            </v-form-group>

            <v-form-group :error="form.getError('hsts_include_subdomains')">
                <v-checkbox-switch v-model="form.hsts_include_subdomains">
                    {{ trans('admin/settings.security_headers.hsts_include_subdomains') }}
                </v-checkbox-switch>
            </v-form-group>
        </div>
    </fieldset>

    {{-- Test link --}}
    <a href="https://securityheaders.com/?q={{ urlencode(UrlFactory::getHomepageUrl()) }}"
       target="_blank"
       class="btn btn-flat"
    >
        {{ trans('admin/settings.security_headers.btn_test') }}
        <i class="fa fa-external-link"></i>
    </a>
</div>
