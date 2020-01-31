<?php /** @var \App\Models\Web\Redirect $redirect */ ?>
<tr>
    <td>
        {{ $redirect->show_from ?? $redirect->from }}
        @if ($redirect->url_from)
            <a href="{{ $redirect->url_from }}" target="_blank"><i class="fa fa-external-link-square"></i></a>
        @endif
    </td>
    <td>
        {{ $redirect->show_to ?? $redirect->to }}
    </td>
    <td>{{ $redirect->status_code }}</td>
    <td>{{ $redirect->author->name ?? trans('admin/redirects/general.index.author_system') }}</td>

    @permission(['pages-edit', 'pages-delete'])
    <td class="text-center">
        <ul class="icons-list">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-bars"></i>
                    <i class="fa fa-angle-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-right">
                    @permission('redirects-edit')
                    <li>
                        <a href="{{ route('admin.redirects.edit', $redirect->getKey()) }}">
                            <i class="fa fa-pencil-square-o"></i>
                            {{ trans('admin/redirects/general.index.btn_edit') }}
                        </a>
                    </li>
                    @endpermission
                    @permission('redirects-delete')
                    <li>
                        <v-confirm-action action="delete"
                                          :texts="{{ json_encode(trans('admin/redirects/general.confirm_delete')) }}"
                                          link="{{ route('admin.redirects.delete', $redirect->getKey()) }}"
                        >
                            <i class="fa fa-trash"></i> {{trans('admin/redirects/general.index.btn_delete')}}
                        </v-confirm-action>
                    </li>
                    @endpermission
                </ul>
            </li>
        </ul>
    </td>
    @endpermission
</tr>
