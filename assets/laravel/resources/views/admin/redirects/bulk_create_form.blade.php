@extends('admin.layouts.master')

@section('content')
    <redirects-bulk-create-form submit-url="{{ route('admin.redirects.bulk_create') }}"
                                back-url="{{ route('admin.redirects.index') }}"
                                :status-codes="{{ json_encode($statusCodes) }}"
                                import-example="{{ route('admin.redirects.import_example') }}"
                                :trans="{{ json_encode(trans('admin/redirects/form.bulk_create')) }}"
                                v-cloak
    ></redirects-bulk-create-form>
@endsection

@push('script')
    {!! Html::script(mix('js/redirects-bulk-create.form.js')) !!}
@endpush
