<?php /** @var \Context $context */ ?>
<!DOCTYPE html>
<html class="no-js old-ie">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{ Html::style($context->mix('css/main.css')) }}
</head>
<body>
{!! $content !!}
{{ Html::script($context->mix('js/app.js')) }}
</body>
</html>
