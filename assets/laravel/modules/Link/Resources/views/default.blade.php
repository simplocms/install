<?php /** @var \Modules\Link\Models\Configuration $configuration */ ?>
<a {!! $configuration->getHTMLAttributesString([
    'href' => $configuration->full_url
]) !!}>{{ $configuration->text }}</a>
