<cms-grid-editor ref="gridEditor" :trans="{{ json_encode(trans('admin/grid_editor')) }}"></cms-grid-editor>

@push('script')
    @include('admin.grideditor._options_script')
@endpush
