<div class="panel">
    <div class="panel-body">
        {{-- Redirect from --}}
        <v-form-group :required="true" :error="form.getError('from')">
            {!! Form::label('from', trans("admin/redirects/form.labels.from")) !!}

            <div class="help-block">{!! trans('admin/redirects/form.from_info') !!}</div>

            <div class="row">
                <div class="col-xs-4 col-md-3 col-lg-2 pr-5 no-margin-top">
                    {{ Form::select('from_language', $fromLanguageOptions, null, [
                        'class' => 'form-control',
                        'v-model' => 'form.from_language'
                    ]) }}
                </div>
                <div class="col-xs-8 col-md-9 col-lg-9 pl-5 no-margin-top">
                    {!! Form::text('from', null, [
                        'class' => 'form-control',
                        'maxlength' => '250',
                        'v-model' => 'form.from',
                        'v-maxlength',
                        ':list' => "'urls-of-language-' + form.from_language"
                    ]) !!}

                    @foreach($urlsByLanguage as $languageCode => $urls)
                        <datalist id="urls-of-language-{{ $languageCode }}">
                            @foreach($urls as $url)
                                <option>{{ $url }}</option>
                            @endforeach
                        </datalist>
                    @endforeach
                </div>
            </div>
        </v-form-group>

        {{-- Redirect to --}}
        <v-form-group :required="true" :error="form.getError('to')">
            {!! Form::label('to', trans("admin/redirects/form.labels.to")) !!}

            <div class="help-block">{!! trans('admin/redirects/form.to_info') !!}</div>

            <div class="row">
                <div class="col-xs-4 col-md-3 col-lg-2 pr-5 no-margin-top">
                    {{ Form::select('to_language', $toLanguageOptions, null, [
                        'class' => 'form-control',
                        'v-model' => 'form.to_language'
                    ]) }}
                </div>
                <div class="col-xs-8 col-md-9 col-lg-10 pl-5 no-margin-top">
                    {!! Form::text('to', null, [
                        'class' => 'form-control',
                        'maxlength' => '250',
                        'v-model' => 'form.to',
                        'v-maxlength'
                    ]) !!}
                </div>
            </div>
        </v-form-group>

        {{-- Status code --}}
        <v-form-group :required="true" :error="form.getError('status_code')">
            {!! Form::label('status_code', trans("admin/redirects/form.labels.status_code")) !!}
            {!! Form::select('status_code', $statusCodes, null, [
                'class' => 'form-control',
                'v-model' => 'form.status_code'
            ]) !!}
        </v-form-group>

    </div>
</div>
