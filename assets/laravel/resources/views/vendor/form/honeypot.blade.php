<div style="width: 0;height: 0;overflow: hidden;">
    {{ Form::email("email_honeypot", null, [
        'class' => 'form-control',
        'placeholder' => 'Honeypot',
        'id' => ($honeypotId = 'honeypot-' . time()),
        'onfocus' => "this.blur();",
        'tabindex' => -1,
        'autocomplete' => 'new-password'
    ]) }}

    <script>
        !function(){for(var e=document.getElementById("{{ $honeypotId }}"),t=e.parentNode.parentNode;t.tagName&&"FORM"!==t.tagName;)t=t.parentNode;"FORM"===t.tagName&&t.addEventListener("submit",function(){e.value.length||(e.value="{{ Hash::make('honeypot') }}@simplo.cz")})}();
    </script>
    {{-- NOT MINIFIED:
    <script>
        (function () {
            var honeypotInput = document.getElementById('{{ $honeypotId }}');
            var formNode = honeypotInput.parentNode.parentNode;

            while(formNode.tagName && formNode.tagName !== 'FORM')
            {
                formNode = formNode.parentNode;
            }

            if (formNode.tagName === 'FORM') {
                formNode.addEventListener("submit", function() {
                    if (!honeypotInput.value.length) {
                        honeypotInput.value = "{{ Hash::make('honeypot') }}" + "@simplo.cz";
                    }
                });
            }
        })();
    </script>
    --}}
</div>
