@extends('admin.layouts.master')

@section('content')
    <settings-form :trans="{{ json_encode(trans('admin/settings')) }}"
                   :settings="{{ json_encode($settings) }}"
                   inline-template
                   v-cloak
    >
        <v-form :form="form"
                method="put"
                action="{{ route('admin.settings.update') }}"
                @success="onSaved"
        >
            <v-tabs class="nav-tabs-custom" no-fade>
                <v-tab title="{{ trans('admin/settings.tabs.general') }}"
                       href="#general"
                       active
                >
                    @include('admin.settings._general')
                </v-tab>
                <v-tab title="{{ trans('admin/settings.tabs.seo') }}"
                       href="#seo"
                >
                    @include('admin.settings._seo')
                </v-tab>
                <v-tab title="{{ trans('admin/settings.tabs.og') }}"
                       href="#open-graph"
                >
                    @include('admin.settings._open_graph')
                </v-tab>
                <v-tab title="{{ trans('admin/settings.tabs.security_headers') }}"
                       href="#security-headers"
                >
                    @include('admin.settings._security_headers')
                </v-tab>
                <v-tab title="{{ trans('admin/settings.tabs.search') }}"
                       href="#search"
                >
                    @include('admin.settings._search')
                </v-tab>
            </v-tabs>

            <div class="form-group mt15">
                <button type="submit" class="btn bg-teal-400 btn-labeled">
                    <b><i class="fa fa-save"></i></b> {{ trans('admin/settings.btn_save') }}
                </button>
            </div>
        </v-form>
    </settings-form>
@endsection

@push('script')
    @include('admin.vendor.form._scripts')
@endpush
