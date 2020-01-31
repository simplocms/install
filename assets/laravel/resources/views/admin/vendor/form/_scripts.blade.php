@if(isset($_FORM_SCRIPTS) && is_array($_FORM_SCRIPTS))
    @foreach ($_FORM_SCRIPTS as $_FORM_SCRIPT)
        {!! Html::script($_FORM_SCRIPT) !!}
    @endforeach
@endif