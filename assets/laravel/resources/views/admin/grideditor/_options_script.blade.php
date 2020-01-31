<script>
    window.gridEditorOptions = function () {
        return {
            urls: {
                modulePreviews: "{!! $_GE_URL_PREVIEWS !!}",
                editModule: "{!! $_GE_URL_EDIT !!}",
                validateModule: "{!! $_GE_URL_VALIDATION !!}",
                entityConfiguration: "{!! $_GE_URL_ENTITY!!}",
                switchVersion: "{!! $_GE_URL_VERSION_SWITCH !!}",
                validateUniversalModule: "{!! $_GE_URL_UNIVERSAL_VALIDATION !!}",
                editUniversalModule: "{!! $_GE_URL_UNIVERSAL_EDIT !!}",
                universalModulePreviews: "{!! $_GE_URL_UNIVERSAL_PREVIEWS !!}",
            },
            content: {!! $_GE_CONTENT ?: '[]' !!},
            modules: {!! $_GE_MODULES !!},
            universalModules: {!! $_GE_UNIVERSAL_MODULES !!},
            versions: {!! $_GE_VERSIONS ? json_encode($_GE_VERSIONS) : "[]" !!},
            useVersions: {{ $_GE_USE_VERSIONS ? 'true' : 'false' }},
            canEditLayout: {{ $_GE_CAN_EDIT_LAYOUT ? 'true' : 'false' }},
            allowedTags: {!! json_encode(config('admin.grideditor.allowed_tags')) !!}
        };
    };
</script>