@if(isset($_FORM_STYLES) && is_array($_FORM_STYLES))
    @foreach ($_FORM_STYLES as $_FORM_STYLE)
        {{ Html::style($_FORM_STYLE) }}
    @endforeach
@endif