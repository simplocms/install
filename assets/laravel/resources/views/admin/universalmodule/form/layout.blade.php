<?php /** @var \App\Services\UniversalModules\UniversalModule $module */ ?>
@include('admin.vendor.form.panel_errors')

<v-tabs class="nav-tabs-custom" no-fade>
    {{-- General --}}
    <v-tab title="{{ trans('admin/universal_modules.form.tabs.general') }}"
           href="#general"
           active
    >
        @include('admin.universalmodule.form._tab_general')
    </v-tab>

    @if ($module->hasUrl())
        {{-- SEO --}}
        <v-tab title="{{ trans('admin/universal_modules.form.tabs.seo') }}"
               href="#seo"
        >
            @include('admin.universalmodule.form._tab_seo')
        </v-tab>

        {{-- OpenGraph --}}
        <v-tab title="{{ trans('admin/universal_modules.form.tabs.og') }}"
               href="#open-graph"
        >
            @include('admin.universalmodule.form._tab_og_tags')
        </v-tab>
    @endif

</v-tabs>
