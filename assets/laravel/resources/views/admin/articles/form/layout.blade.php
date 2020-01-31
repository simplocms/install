@include('admin.vendor.form.panel_errors')

<div class="row">
    <div class="no-padding-right" :class="[showPublishingInputs ? 'col-md-9' : 'col-xs-12']">
        <v-tabs class="nav-tabs-custom" @input="tabActivated" no-fade>
            {{-- General --}}
            <v-tab title="{{ trans('admin/article/form.tabs.general') }}"
                   href="#general"
                   active
            >
                @include('admin.articles.form._tab_general')
            </v-tab>

            {{-- Grid --}}
            @if ($flag->use_grid_editor)
                <v-tab title="{{ trans('admin/article/form.tabs.grid') }}"
                       href="#grid"
                >
                    @include('admin.articles.form._tab_grid_editor')
                </v-tab>
            @endif

            {{-- Photogallery --}}
            <v-tab title="{{ trans('admin/article/form.tabs.photogallery') }}"
                   href="#photogallery"
            >
                @include('admin.articles.form._tab_photogallery')
            </v-tab>

            {{-- SEO --}}
            <v-tab title="{{ trans('admin/article/form.tabs.seo') }}"
                   href="#seo"
            >
                @include('admin.articles.form._tab_seo')
            </v-tab>

            {{-- OpenGraph --}}
            <v-tab title="{{ trans('admin/article/form.tabs.og') }}"
                   href="#open-graph"
            >
                @include('admin.articles.form._tab_og_tags')
            </v-tab>
        </v-tabs>
    </div>
    <div class="col-md-3 mt-20" v-show="showPublishingInputs">
        <div class="panel panel-default mt-20">
            <div class="panel-heading text-uppercase">{{ trans('admin/article/form.tabs.state') }}</div>
            <div class="panel-body">
                @include('admin.articles.form._tab_state')
            </div>
        </div>

        <div class="panel panel-default mt-20">
            <div class="panel-heading text-uppercase">{{ trans('admin/article/form.tabs.category') }}</div>
            <div class="panel-body">
                @include('admin.articles.form._tab_categories')
            </div>
        </div>
    </div>
</div>
