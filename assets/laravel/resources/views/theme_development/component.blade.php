@extends('theme_development.@layout')

@section('content')
    <nav class="navbar">
        <div class="container-fluid">
            <ul class="nav navbar-nav">
                <li>
                    <a href="{{ route('theme_development.index') }}" class="btn btn-link">
                        <i class="fa fa-arrow-left"></i>
                        {{ trans('theme_development.btn_list') }}
                    </a>
                </li>
                <li class="info">
                    {{ trans('theme_development.component_info', compact('name')) }}
                </li>
            </ul>
        </div>
    </nav>

    <section class="component-content">
        <div><textarea id="html-box">{!! $html !!}</textarea></div>
        <div><textarea id="scss-box">{!! $scss !!}</textarea></div>
        <div>
            <iframe src="{!! route('theme_development.component', ['name' => $name, 'preview']) !!}"></iframe>
        </div>
    </section>

    {{ Html::script(url('plugin/js/beautify.js')) }}
    {{ Html::script(url('plugins/ace/ace.js')) }}
    <script>
        const htmlBox = document.getElementById('html-box');
        htmlBox.value = html_beautify(htmlBox.value);

        const htmlEditor = ace.edit(htmlBox);
        htmlEditor.setTheme("ace/theme/monokai");
        htmlEditor.getSession().setMode("ace/mode/html");
        htmlEditor.setShowPrintMargin(false);

        const scssBox = document.getElementById('scss-box');
        const scssEditor = ace.edit(scssBox);
        scssEditor.setTheme("ace/theme/monokai");
        scssEditor.getSession().setMode("ace/mode/scss");
        scssEditor.setShowPrintMargin(false);
    </script>
@endsection
