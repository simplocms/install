<h6 class="panel-title mb-5">{{trans('admin/pages/form.planning_tab.title')}}</h6>
<p class="mb-15">
    {!! trans('admin/pages/form.planning_tab.help_text') !!}
</p>

{{-- Datum a čas publikování stránky --}}
<div class="form-group {{ $errors->has('publish_at_date') || $errors->has('publish_at_time') ? 'has-error' : '' }}">
    {!! Form::label('publish_at', trans('admin/pages/form.labels.publish_at')) !!}
    <div>
        <div class="input-group input-group-date">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            <date-picker class="form-control"
                         name="{{ $name = 'publish_at_date' }}"
                         placeholder="{{ trans("admin/pages/form.placeholders.$name") }}"
                         v-model="form.publish_at_date"
            ></date-picker>
            @include('admin.vendor.form.field_error')
        </div>

        <div class="input-group input-group-time">
            <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
            <time-picker class="form-control"
                         name="{{ $name = 'publish_at_time' }}"
                         v-model="form.publish_at_time"
                         placeholder="{{ trans("admin/pages/form.placeholders.$name") }}"
            ></time-picker>
            @include('admin.vendor.form.field_error')
        </div>

        <br class="clearfix">
    </div>
    <br class="clearfix">
</div>

{{-- Zobrazit položky pro odpublikování stránky? --}}
<v-checkbox-switch v-model="form.set_unpublish_at" name="set_unpublish_at">
    {{ trans('admin/pages/form.labels.set_unpublish_at') }}
</v-checkbox-switch>

{{-- Datum a čas odpublikování stránky --}}
<div id="set_unpublish_at"
     class="collapse"
     :class="[form.set_unpublish_at ? 'in' : 'out']"
>
    <div class="form-group {{ $errors->has('unpublish_at_date') || $errors->has('unpublish_at_time') ? 'has-error' : '' }}">
        {!! Form::label('unpublish_at', trans('admin/pages/form.labels.unpublish_at')) !!}
        <div>
            <div class="input-group input-group-date">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <date-picker class="form-control"
                             name="{{ $name = 'unpublish_at_date' }}"
                             v-model="form.unpublish_at_date"
                             placeholder="{{ trans("admin/pages/form.placeholders.$name") }}"
                ></date-picker>
                @include('admin.vendor.form.field_error')
            </div>

            <div class="input-group input-group-time">
                <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                <time-picker class="form-control"
                             name="{{ $name = 'unpublish_at_time' }}"
                             v-model="form.unpublish_at_time"
                             placeholder="{{ trans("admin/pages/form.placeholders.$name") }}"
                ></time-picker>
                @include('admin.vendor.form.field_error')
            </div>

            <br class="clearfix mb10">
        </div>
    </div>
</div>
