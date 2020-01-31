@extends('admin.layouts.master')

@section('content')
<page-modules :trans="{{ json_encode(trans('admin/modules')) }}" inline-template>
    <!-- Default ordering -->
    <div class="panel panel-flat">
        <table class="table datatable-sorting">
            <thead>
            <tr>
                <th>{{ trans('admin/modules.index.table_columns.name') }}</th>
                @permission(['modules-toggle'])
                <th width="180">{{ trans('admin/modules.index.table_columns.status') }}</th>
                @endpermission
                @permission(['modules-install'])
                <th width="170">{{ trans('admin/modules.index.table_columns.installation') }}</th>
                @endpermission
            </tr>
            </thead>
            <tbody>
            @foreach($modules as $module)

            <tr>
                <td>{{ $module->getName() }}</td>
                @permission(['modules-toggle'])
                <td>
                    @if ($module->installation)
                        <a href="{{ route('admin.modules.toggle', $module->installation->id) }}" class="automatic-post">
                            {!! $module->installation->enabled ? '<span class="label label-success">' . trans('admin/modules.status.enabled') .'</span>'
                        : '<span class="label label-danger">' . trans('admin/modules.status.disabled') . '</span>' !!}
                        </a>
                    @else
                        <span class="label label-default">{{trans('admin/modules.status.disabled')}}</span>
                    @endif
                </td>
                @endpermission
                @permission(['modules-install'])
                <td>
                    @if ($module->installation)
                        <a href="{{ route('admin.modules.uninstall', $module->installation->id) }}" class="action-uninstall" id="{{$module->installation->id}}">
                            <span class="label label-danger">{{ trans('admin/modules.index.btn_uninstall') }}</span>
                        </a>
                    @else
                        <a href="{{ route('admin.modules.install', $module->name) }}" class="automatic-post">
                            <span class="label label-success">{{ trans('admin/modules.index.btn_install') }}</span>
                        </a>
                    @endif
                </td>
                @endpermission
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</page-modules>
@endsection

@push('script')
    {!! Html::script(mix('js/modules.page.js')) !!}
@endpush
