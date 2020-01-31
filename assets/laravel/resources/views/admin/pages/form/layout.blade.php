<?php /** @var \App\Models\Page\Page $page */ ?>

@include('admin.vendor.form.panel_errors')

@if ($page->hasTestingCounterpart())
<binary-switch
    size="sm"
    label-on="A"
    label-off="B"
    :value-on="{{ $page->getKey() }}"
    :value-off="{{ $page->testing_b_id }}"
    @input="switchingTestingVariant"
    :value="testing.activeVariantId"
    class="pull-right"
></binary-switch>
@endif

<v-tabs class="nav-tabs-custom" no-fade>
    {{-- General --}}
    <v-tab title="{{ trans('admin/pages/form.tabs.details') }}"
           href="#general"
           active
    >
        @include('admin.pages.form._tab_details')
    </v-tab>

    {{-- SEO --}}
    <v-tab title="{{ trans('admin/pages/form.tabs.seo') }}"
           href="#seo"
    >
        @include('admin.pages.form._tab_seo')
    </v-tab>

    {{-- Planning --}}
    <v-tab title="{{ trans('admin/pages/form.tabs.planning') }}"
           href="#planning"
           :visible="!isTestingCounterpart"
    >
        @include('admin.pages.form._tab_publish')
    </v-tab>

    {{-- OpenGraph --}}
    <v-tab title="{{ trans('admin/article/form.tabs.og') }}"
           href="#open-graph"
    >
        @include('admin.pages.form._tab_og_tags')
    </v-tab>

    {{-- Grid --}}
    <v-tab title="{{ trans('admin/pages/form.tabs.grid') }}"
           href="#grid"
    >
        @include('admin.pages.form._tab_grid')
    </v-tab>
</v-tabs>
