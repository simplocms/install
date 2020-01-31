@if(View::exists('theme::config.form'))
    @include('theme::config.form')
@endif

<div class="modal fade" id="themesModal" tabindex="-1" ref="themeModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    {{ trans('admin/settings.change_theme_modal.title') }}
                </h4>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th>{{ trans('admin/settings.change_theme_modal.table_columns.name') }}</th>
                        <th class="text-right">
                            {{ trans('admin/settings.change_theme_modal.table_columns.activation') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($themes as $theme)
                        <tr>
                            <td>{{ $theme->name }}</td>
                            <td class="text-right">
                                @if($theme->id == $defaultTheme->id)
                                    <span class="text-muted">
                                            {{ trans('admin/settings.change_theme_modal.status_active') }}
                                        </span>
                                @else
                                    <a href="{{ route('admin.settings.switch_theme', $theme->id) }}"
                                       @click.prevent="changeTemplate"
                                    >
                                        {{ trans('admin/settings.change_theme_modal.btn_activate') }}
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
