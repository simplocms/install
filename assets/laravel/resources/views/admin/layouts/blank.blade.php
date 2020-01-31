<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('admin.title') }} {{ $page_title ?? '' }}</title>

    {!! Html::style(mix('css/admin.css')) !!}
    {!! Html::script(mix('js/bootstrap.js')) !!}

    @stack('style')

</head>
<body class="bg-slate-800">
    <div id="app">
        <div class="page-wrapper">
            <div class="box-body">
                @include('flash::message')
            </div>

            <div class="page-container login-container">
                <div class="page-content">
                    <div class="content-wrapper">
                        <div class="content">

                            @yield('content')

                            <div class="footer text-white">
                                {!! str_replace(':version', \App\Services\ComposerParser\ComposerParser::make()->getVersion(), config('admin.copyright')) !!}
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @stack('script')
    {!! Html::script(mix('js/admin.js')) !!}

</body>
</html>
