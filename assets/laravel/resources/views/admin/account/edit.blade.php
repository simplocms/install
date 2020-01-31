@extends('admin.layouts.master')

@section('content')
    <page-account-edit inline-template>
        <div class="row">
            <div class="col-md-6">

                <div class="panel panel-flat">
                    <div class="panel-heading">
                        <h5 class="panel-title">{{trans('admin/account/form.general.title')}}</h5>
                    </div>

                    <div class="panel-body">

                        @include('admin.account._account_form')

                    </div>
                </div>

            </div>

            <div class="col-md-6">

                <div class="panel panel-flat">
                    <div class="panel-heading">
                        <h5 class="panel-title">{{trans('admin/account/form.password.title')}}</h5>
                    </div>

                    <div class="panel-body">

                        @include('admin.account._password_change_form')

                    </div>
                </div>

            </div>

        </div>
    </page-account-edit>
@endsection

@push('script')
{!! Html::script( url('plugin/js/bootstrap-maxlength.js') ) !!}

<script>
window.pageAccountEditOptions = function () {
    return {
        defaultThumbnailSrc: "{!! Gravatar::get(auth()->user()->email) !!}",
        imageSrc: "{!! auth()->user()->image_url !!}",
        imageSelected: {!! auth()->user()->hasCustomImage() ? 'true' : 'false' !!}
    }
}
</script>

{!! Html::script(mix('js/account.page.js')) !!}

@endpush
