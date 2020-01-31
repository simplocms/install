@extends('admin.layouts.master')

@section('content')
<roles-form inline-template>
    <div class='row'>
        <div class='col-md-12'>
            <div class="box-body">
                {!! Form::model($role, ['url' => $submitUrl]) !!}

                @include('admin.roles._form_content')

                <div class="form-group mt15">
                    {!! Form::button($role->exists ? trans('admin/roles/form.btn_update') : trans('admin/roles/form.btn_create'), [
                        'class' => 'btn bg-teal-400',
                        'type' => 'submit',
                        'id' => 'btn-submit-edit'
                    ]) !!}
                    <a href="{!! route('admin.roles') !!}" title="cancel" class='btn btn-default'>
                        {{ trans('admin/roles/form.btn_cancel') }}
                    </a>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</roles-form>
@endsection

@push('script')
    @include('admin.vendor.form._scripts')
@endpush
