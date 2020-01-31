@extends('admin.layouts.master')

@section('content')
    <?php
    $canEdit = auth()->user()->can('languages-edit');
    ?>
    <page-languages inline-template>
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-flat">
                    <table class="table ">
                        <thead>
                        <tr>
                            <th width="130">{{ trans('admin/languages/general.index.table_columns.toggle') }}</th>
                            <th width="130">{{ trans('admin/languages/general.index.table_columns.flag') }}</th>
                            <th width="180">{{ trans('admin/languages/general.index.table_columns.name') }}</th>
                            <th width="180">{{ trans('admin/languages/general.index.table_columns.code') }}</th>
                            <th>{{ trans('admin/languages/general.index.table_columns.default') }}</th>
                            @permission(['languages-edit', 'languages-delete'])
                            <th>{{ trans('admin/languages/general.index.table_columns.actions') }}</th>
                            @endpermission
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($languages as $language)
                            <tr>
                                <td>
                                    @if($canEdit)
                                        <a href="{{ route('admin.languages.toggle', $language->id) }}"
                                           class="automatic-post"
                                           id="{{$language->id}}"
                                        >
                                            @endif
                                            @if ($language->enabled)
                                                <span class="label label-success"
                                                      title="{{ trans('admin/languages/general.index.title_disable') }}"
                                                >
                                                {{ trans('admin/languages/general.status.enabled') }}
                                            </span>
                                            @else
                                                <span class="label label-danger"
                                                      title="{{ trans('admin/languages/general.index.title_enable') }}"
                                                >
                                                {{ trans('admin/languages/general.status.disabled') }}
                                            </span>
                                            @endif
                                            @if($canEdit)</a>@endif
                                </td>
                                <td>
                                    <img class="img-responsive"
                                         src="/media/images/flags/{{ $language->country_code }}.png"
                                         alt="{{$language->name}}">
                                </td>
                                <td>{{ $language->name }}</td>
                                <td>{{ $language->language_code }}</td>
                                <td>
                                    @if($language->default)
                                        <span class="label label-info">
                                            {{ trans('admin/languages/general.status.default') }}
                                        </span>
                                    @endif
                                </td>

                                @permission(['languages-edit', 'languages-delete'])
                                <td class="text-center">
                                    <ul class="icons-list">
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                <i class="fa fa-bars"></i>
                                                <i class="fa fa-angle-down"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                @permission('languages-edit')
                                                <li>
                                                    <a href="{{ route('admin.languages.edit', [ $language->id ]) }}">
                                                        <i class="fa fa-pencil-square-o"></i>
                                                        {{ trans('admin/languages/general.index.btn_edit') }}
                                                    </a>
                                                </li>
                                                @endpermission
                                                @permission('languages-delete')
                                                <li>
                                                    <v-confirm-action action="delete"
                                                                      :texts="{{ json_encode(trans('admin/languages/general.confirm_delete')) }}"
                                                                      link="{{ route('admin.languages.delete', $language->id) }}"
                                                    >
                                                        <i class="fa fa-trash"></i>
                                                        {{ trans('admin/languages/general.index.btn_delete') }}
                                                    </v-confirm-action>
                                                </li>
                                                @endpermission
                                                @if($language->enabled)
                                                    <li>
                                                        <a href="{{ route('admin.languages.default', $language->id) }}"
                                                           class="automatic-post">
                                                            <i class="fa fa-bullseye"></i>
                                                            {{ trans('admin/languages/general.index.btn_set_default') }}
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </li>
                                    </ul>
                                </td>
                                @endpermission
                            </tr>
                        @endforeach


                        </tbody>
                    </table>
                </div>

                @permission('languages-create')
                <a href="{{ route('admin.languages.create') }}" class="btn bg-teal-400 btn-labeled">
                    <b><i class="fa fa-pencil-square-o"></i></b> {{ trans('admin/languages/general.index.btn_create') }}
                </a>
                @endpermission
            </div>

            <div class="col-md-6">
                <div class="panel panel-flat">
                    <div class="panel-heading">
                        <h2 class="panel-title">{{trans('admin/languages/general.settings.title')}}</h2>
                        <p>{{trans('admin/languages/general.settings.help_text')}}</p>

                        {!! Form::open(['route'=>'admin.languages.settings', 'id' => 'language-settings-form', 'class' => 'automatic-post']) !!}
                        <div class="radio">
                            <label>
                                <?php
                                $value = config('admin.language_url.directory');
                                $isDirectory = $languageDisplay === $value;
                                ?>
                                {!! Form::radio('language_display', $value, $isDirectory, [
                                    'type'=>'radio',
                                    'class'=>'styled',
                                    ':disabled' => $canEdit ? 'false' : 'true'
                                ]) !!}
                                {{ trans('admin/languages/general.settings.option_directory') }}
                                <kbd>{{ trans('admin/languages/general.settings.example_domain') }}/cs</kbd> /
                                <kbd>{{ trans('admin/languages/general.settings.example_domain') }}/en</kbd>
                            </label>
                        </div>

                        <div class="checkbox"
                             {!! $isDirectory ? '' : 'style="display: none"' !!}
                             id="default-language-input"
                        >
                            <label>
                                {!! Form::checkbox('default_language_hidden', 1, $defaultLanguageHidden,[
                                    'type'=>'checkbox',
                                    'class'=>'styled',
                                    ':disabled' => $canEdit ? 'false' : 'true'
                                ]) !!}
                                {{ trans('admin/languages/general.settings.show_default') }}
                            </label>
                        </div>

                        <div class="radio">
                            <label>
                                <?php
                                $value = config('admin.language_url.subdomain');
                                $isSelected = $languageDisplay === $value;
                                ?>
                                {!! Form::radio('language_display', $value, $isSelected, [
                                    'type'=>'radio',
                                    'class'=>'styled',
                                    ':disabled' => $canEdit ? 'false' : 'true'
                                ]) !!}
                                {{ trans('admin/languages/general.settings.option_subdomain') }}
                                <kbd>cs.{{ trans('admin/languages/general.settings.example_domain') }}</kbd> /
                                <kbd>en.{{ trans('admin/languages/general.settings.example_domain') }}</kbd>
                            </label>
                        </div>

                        <div class="radio">
                            <label>
                                <?php
                                $value = config('admin.language_url.domain');
                                $isSelected = $languageDisplay === $value;
                                ?>
                                {!! Form::radio('language_display', $value, $isSelected, [
                                    'type'=>'radio',
                                    'class'=>'styled',
                                    ':disabled' => $canEdit ? 'false' : 'true'
                                ]) !!}
                                {{ trans('admin/languages/general.settings.option_domain') }}
                                <kbd>{{ trans('admin/languages/general.settings.example_domain', [], 'cs') }}</kbd> /
                                <kbd>{{ trans('admin/languages/general.settings.example_domain', [], 'en') }}</kbd>
                            </label>
                        </div>

                        @permission('languages-edit')
                        {!! Form::button(trans('admin/languages/general.settings.btn_save'),[
                            'class'=>'btn bg-teal-400',
                            'type'=>'submit',
                            'id'=>'btn-submit-settings'
                        ]) !!}
                        @endpermission

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </page-languages>
@endsection

@push('script')
    <script>
        window.pageLanguageOptions = function () {
            return {
                urlTypes: {!! json_encode(config('admin.language_url')) !!}
            };
        };
    </script>
    {!! Html::script(mix('js/languages.page.js')) !!}
@endpush
