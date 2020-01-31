<!DOCTYPE html>
<html>
<head>
    <title>{{ $title ?? trans('theme_development.index.title') }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="SIMPLO s.r.o.">

    {{ Html::style(mix('css/theme-development.css')) }}

</head>
<body>

@yield('content')

</body>
</html>
