@extends('admin.layouts.master')

@section('content')
    <div class='row'>
        <div class="col-md-6 col-md-offset-3">
            <dashboard-profiles-list inline-template>
                <div class="panel panel-flat" id="ga-profiles-list">

                    <div class="panel-heading">
                        <h5 class="panel-title">{{trans('admin/dashboard.choose_profile.title')}}</h5>
                    </div>

                    <div class="col-xs-12">
                        {{ trans('admin/dashboard.choose_profile.help_text') }}
                        <br>
                        <label class="alert">
                            <input checked="checked" name="enable_tracking" type="checkbox" value="1" v-model="enableTracking">
                            {{ trans('admin/dashboard.choose_profile.include_measuring_code') }}
                        </label>
                    </div>

                    <div class="clearfix"></div>

                    <ul class="media-list media-list-linked">

                        @foreach($profilesList as $profile)
                        <li class="media">
                            <a href="#" class="media-link" @click.prevent="selectProfile('{{ $profile->accountId }}', '{{ $profile->property }}', '{{ $profile->id }}')">
                                <div class="media-body">
                                    <div class="media-heading text-semibold">
                                        {{ $profile->name }}
                                    </div>
                                    <span class="text-muted">{{ $profile->url }}</span>
                                </div>
                                <div class="media-right media-middle">
                                    <span class="label label-primary">{{ $profile->property }}</span>
                                </div>
                            </a>
                        </li>
                        @endforeach

                    </ul>
                </div>
            </dashboard-profiles-list>
        </div>
    </div>
@endsection

@push('script')
<script>
window.dashboardProfilesListOptions = function () {
    return {
        submitUrl: "{{ route('admin.dashboard.profiles') }}"
    }
}
</script>
{{ Html::script(mix('js/dashboard.profiles-list.page.js')) }}
@endpush
