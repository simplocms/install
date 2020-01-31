@include('admin.vendor.form.panel_errors')

<div class="tabbable tab-content-bordered">
    <ul class="nav nav-tabs nav-tabs-highlight">
        <li class="active">
            <a href="#details-tab" data-toggle="tab">
                {{ trans('admin/widgets/form.tabs.details') }}
            </a>
        </li>
        <li>
            <a href="#content-tab" data-toggle="tab">{{ trans('admin/widgets/form.tabs.content') }}</a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane has-padding active" id="details-tab">
            <v-form-group :error="form.getError('id')" :required="true">
                {!! Form::label('id', trans("admin/widgets/form.labels.id")) !!}
                {!! Form::text('id', null, [
                    'class' => 'form-control',
                    'maxlength' => '50',
                    'v-model' => 'form.id',
                    'v-maxlength'
                ]) !!}
            </v-form-group>

            <v-form-group :error="form.getError('name')" :required="true">
                {!! Form::label('name', trans("admin/widgets/form.labels.name")) !!}
                {!! Form::text('name', null, [
                    'class' => 'form-control',
                    'maxlength' => '255',
                    'v-model' => 'form.name',
                    'v-maxlength'
                ]) !!}
            </v-form-group>
        </div>

        <div class="tab-pane has-padding" id="content-tab">
            @include('admin.grideditor._input')
        </div>
    </div>
</div>
