<?php
/** @var \Context $context */
/** @var \App\Structures\DataTypes\Breadcrumb[]|\App\Structures\Collections\BreadcrumbsCollection $breadcrumbs */
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <ol class="breadcrumb">
                <li>
                    <a href="{!! UrlFactory::getHomepageUrl() !!}">
                        {{ $context->trans('theme.breadcrumbs_homepage') }}
                    </a>
                </li>
                @foreach($breadcrumbs as $breadcrumb)
                    <li @if ($breadcrumb === $breadcrumbs->last()) class="active" @endif>
                        @if ($breadcrumb->getUrl())
                            <a href="{!! $breadcrumb->getUrl() !!}">{{ $breadcrumb->getText() }}</a>
                        @else
                            {{ $breadcrumb->getText() }}
                        @endif
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
</div>
