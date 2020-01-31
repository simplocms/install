<?php /** @var \Context $context */ ?>
@if(isset($MenuPrimary))
<nav class="navbar navbar-default">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-menu" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ UrlFactory::getHomepageUrl() }}">
                <img alt="logo"
                     height="20"
                     src="{{ \App\Structures\Enums\SingletonEnum::settings()->makeImageLink('logo')->resize(100, 20) }}"
                >
            </a>
        </div>
        <div class="collapse navbar-collapse" id="navbar-menu">
            <ul class="nav navbar-nav navbar-left">
                @foreach($MenuPrimary->roots() as $item)
                    <li class="{{ $item->hasChildren() ? 'dropdown' : '' }} {{ $item->attributes['class'] }}">
                        @if($item->hasChildren())
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span>{!! $item->title !!}</span></a>

                            <ul class="dropdown-menu">
                                @foreach($item->children() as $subitem)
                                    <li class="{{ $item->attributes['class'] }}">
                                        <a href="{!! $subitem->url() !!}">{!! $subitem->title !!} </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <a href="{!! $item->url() !!}" class="{{ $item->isActive ? 'active' : '' }}"><span>{!! $item->title !!}</span></a>
                        @endif
                    </li>
                @endforeach
                <li>
                    <a href="{{ UrlFactory::getRssFeedUrl() }}"
                       type="application/rss+xml"
                    >RSS</a>
                </li>
            </ul>

            <form class="navbar-form navbar-right" method="GET" action="{{ UrlFactory::getSearchUrl() }}">
                <div class="input-group">
                    <input type="text"
                           class="form-control"
                           value="{{ request('q') }}"
                           placeholder="{{ $context->trans('theme.search_placeholder') }}"
                           name="q"
                    >
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="submit">🔍</button>
                    </span>
                </div>
            </form>
        </div>
    </div>
</nav>
@endif
