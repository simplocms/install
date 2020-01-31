<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{{ asset('media/admin/images/favicon.ico') }}" />

    <title>{{ config('admin.title') }} {{ $pageTitle ?? '' }}</title>

    {!! Html::style(mix('css/admin.css')) !!}
    {!! Html::script(mix('js/bootstrap.js')) !!}

    @stack('style')

</head>

<body>

<div id="app">
    <div class="page-wrapper">
        @include('admin.vendor._main_navbar')

        <div class="page-container">

            <div class="page-content">

                <div class="sidebar sidebar-main">
                    <div class="sidebar-content">

                        <!-- User menu -->
                        <div class="sidebar-user">
                            <div class="category-content">
                                <div class="media">
                                    <span class="media-left">
                                        <img src="{!! auth()->user()->image_url !!}" class="img-circle img-sm" alt="">
                                    </span>
                                    <div class="media-body">
                                        <span
                                            class="media-heading text-semibold login-name">{{ Auth::user()->name }}</span>
                                    </div>

                                    <div class="media-right media-middle">
                                        <ul class="icons-list">
                                            <li>
                                                <a href="{{ route('admin.auth.logout') }}" class="automatic-post">
                                                    <i class="fa fa-power-off"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /user menu -->

                        @include('admin.vendor._menu')

                    </div>
                </div>
                <!-- /main sidebar -->


                <!-- Main content -->
                <div class="content-wrapper">

                    <!-- Page header -->
                    <div class="page-header">
                        <div class="page-header-content">
                            <div class="page-title">
                                <h4>
                                    <i class="fa fa-arrow-circle-o-left"></i>
                                    <span class="text-semibold">{{ $pageTitle ?? "Page Title" }}</span>
                                    @if($pageDescription)
                                        - {{ $pageDescription }}
                                    @endif
                                </h4>
                            </div>
                        </div>

                        <div class="breadcrumb-line">
                            <ul class="breadcrumb">
                                <li>
                                    <a href="{{ route('admin') }}">
                                        <i class="fa fa-home"></i> Admin
                                    </a>
                                </li>
                                <li class="active">{{ $pageTitle ?? "Page Title" }}</li>
                            </ul>

                            <ul class="breadcrumb-elements">
                                @yield('breadcrumb-elements')
                            </ul>
                        </div>
                    </div>

                    <div class="content">

                        @yield('content')

                        <div class="footer text-muted">
                            {!! str_replace(':version', \App\Services\ComposerParser\ComposerParser::make()->getVersion(), config('admin.copyright')) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('admin.media-library._prompt')
    </div>
</div>

@include('admin.vendor._unlock_account_modal')

@include('admin.vendor.js-config')

@stack('script')

{!! Html::script(mix('js/admin.js')) !!}

@stack('after-app')

</body>
</html>
