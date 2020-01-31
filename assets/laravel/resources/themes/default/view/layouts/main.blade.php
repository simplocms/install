<?php /** @var \App\Models\Web\ViewData $data */ ?>
<!DOCTYPE html>

<!--[if lte IE 8]> <html class="no-js old-ie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="{{ $data->language->language_code }}">
<!--<![endif]-->

<head>
    <title>{{ $data->title }}</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="SIMPLO s.r.o.">

    {{-- AUTOMATIC ICONS --}}
    @include('site.icons')

    {{-- AUTOMATIC META TAGS --}}
    @include('site.common_meta')

    <script>
        document.documentElement.className =
                document.documentElement.className.replace("no-js", "js");
    </script>

    {{ Html::style($context->mix('css/main.css')) }}

</head>
<body>

    {{-- NAVBAR --}}
    @include('theme::menus.primary')
    {{-- /NAVBAR --}}

    @yield('content')

    {{-- STRUCTURED DATA FOR RICH SNIPPETS --}}
    {!! $data->getStructuredData() !!}

    {{ Html::script($context->mix('js/app.js')) }}

    @include('vendor._ga_tracking_code')
    @stack('scripts')

</body>
</html>
