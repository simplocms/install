<div class="col-xs-12">
    <div class="panel panel-flat">
        <div class="panel-heading">
            <h6 class="panel-title text-semibold">{{trans('admin/dashboard.graphs.title')}}</h6>
        </div>

        <div class="panel-body" id="charts-body" style="display: none">
            <div id="ga-sessions-chart" style="width: 100%; height: 300px;"></div>
            <hr>
            <div class="col-md-6 col-xs-12">
                <div class="row">
                    {{-- Visits / Sesions --}}
                    <div class="col-md-4 col-xs-4">

                        <div class="panel">
                            <div class="panel-body">
                                {{trans('admin/dashboard.graphs.visits_count')}}
                                <strong></strong>
                                <h2 class="no-margin">--</h2>
                                <div id="ga-visits-chart" style="width: 100%; height: 25px;"></div>
                            </div>
                        </div>

                    </div>

                    {{-- Users --}}
                    <div class="col-md-4 col-xs-4">

                        <div class="panel">
                            <div class="panel-body">
                                {{trans('admin/dashboard.graphs.users_count')}}
                                <strong></strong>
                                <h2 class="no-margin">--</h2>
                                <div id="ga-users-chart" style="width: 100%; height: 25px;"></div>

                            </div>
                        </div>

                    </div>

                    {{-- Pageviews --}}
                    <div class="col-md-4 col-xs-4">

                        <div class="panel">
                            <div class="panel-body">
                                {{trans('admin/dashboard.graphs.pageviews')}}
                                <strong></strong>
                                <h2 class="no-margin">--</h2>
                                <div id="ga-pageviews-chart" style="width: 100%; height: 25px;"></div>

                            </div>
                        </div>

                    </div>

                    {{-- Views per session --}}
                    <div class="col-md-4 col-xs-4">

                        <div class="panel">
                            <div class="panel-body">
                                {{trans('admin/dashboard.graphs.views_per_session')}}
                                <strong></strong>
                                <h2 class="no-margin">--</h2>
                                <div id="ga-viewsPerSession-chart" style="width: 100%; height: 25px;"></div>

                            </div>
                        </div>

                    </div>

                    {{-- Bouncer Rate --}}
                    <div class="col-md-4 col-xs-4">

                        <div class="panel">
                            <div class="panel-body">
                                {{trans('admin/dashboard.graphs.bouncer_rate')}}
                                <strong></strong>
                                <h2 class="no-margin">--</h2>
                                <div id="ga-BounceRate-chart" style="width: 100%; height: 25px;"></div>

                            </div>
                        </div>

                    </div>

                    {{-- Organic searches --}}
                    <div class="col-md-4 col-xs-4">

                        <div class="panel">
                            <div class="panel-body">
                                {{trans('admin/dashboard.graphs.organic_searches_count')}}
                                <strong></strong>
                                <h2 class="no-margin">--</h2>
                                <div id="ga-organicSearches-chart" style="width: 100%; height: 25px;"></div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xs-12">
                <div id="ga-returningUsers-chart" style="width: 100%; height: 300px;"></div>
            </div>

        </div>
    </div>
</div>

@section('breadcrumb-elements')
    @if ($canManage)
        <li>
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-cog"></i> {{ trans('admin/dashboard.graphs.buttons.settings') }}
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-right">
                <li>
                    <a href="{{ route('admin.dashboard.profiles') }}">
                        {{ trans('admin/dashboard.graphs.buttons.switch_ga_profile') }}
                    </a>
                </li>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="{{ route('admin.dashboard.off') }}" class="automatic-post">
                        {{ trans('admin/dashboard.graphs.buttons.disconnect_ga') }}
                    </a>
                </li>
            </ul>
        </li>
    @endif
@endsection

@push('script')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script>
    var dashboardOptions = {
        chartDataUrl: "{{ route('admin.dashboard.chartData') }}",
        trans: {!! json_encode(trans('admin/dashboard.graphs')) !!}
    };
</script>

{{ Html::script(mix('js/dashboard.page.js')) }}
@endpush
