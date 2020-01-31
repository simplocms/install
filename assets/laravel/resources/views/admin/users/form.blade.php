@extends('admin.layouts.master')

@section('content')
<users-form inline-template>
    <div class='row'>
        <div class='col-md-12'>
            <div class="box-body">
                {!! Form::model($user, ['url' => $submitUrl]) !!}

                @include('admin.users._form_content')

                <div class="form-group mt15">
                    {!! Form::button($user->exists ? trans('admin/users/form.btn_update') : trans('admin/users/form.btn_create'), [
                        'class' => 'btn bg-teal-400',
                        'type' => 'submit'
                    ] ) !!}
                    <a href="{!! route('admin.users') !!}" class='btn btn-default'>
                        {{ trans('admin/users/form.btn_cancel') }}
                    </a>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</users-form>
@endsection

@push('script')
    @include('admin.vendor.form._scripts')
@endpush
