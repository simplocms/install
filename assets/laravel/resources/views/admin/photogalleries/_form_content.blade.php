@include('admin.vendor.form.panel_errors')

<div class="tabbable tab-content-bordered">
    <ul class="nav nav-tabs nav-tabs-highlight">
        <li class="active">
            <a href="#tab_details" data-toggle="tab" aria-expanded="true">
                {{trans('admin/photogalleries/form.tabs.details')}}
            </a>
        </li>
        <li>
            <a href="#tab_photogallery" data-toggle="tab" aria-expanded="false">
                {{trans('admin/photogalleries/form.tabs.photogallery')}}
            </a>
        </li>
        <li>
            <a href="#tab_seo" data-toggle="tab" aria-expanded="false">
                {{trans('admin/photogalleries/form.tabs.seo')}}
            </a>
        </li>
        <li>
            <a href="#tab_og" data-toggle="tab" aria-expanded="false">
                {{ trans('admin/photogalleries/form.tabs.og') }}
            </a>
        </li>
        <li>
            <a href="#tab_publish" data-toggle="tab" aria-expanded="false">
                {{trans('admin/photogalleries/form.tabs.planning')}}
            </a>
        </li>
    </ul>

    {{-- GENERAL --}}
    <div class="tab-content">
        <div class="tab-pane has-padding active" id="tab_details">

            {{-- Photogallery title --}}
            <v-form-group :required="true" :error="form.getError('title')">
                {!! Form::label('title', trans("admin/photogalleries/form.labels.title")) !!}
                {!! Form::text('title', null, [
                    'class' => 'form-control maxlength',
                    'maxlength' => '250',
                    ':value' => 'form.title',
                    '@change' => 'onTitleChanged'
                ]) !!}
            </v-form-group>

            {{-- URL slug článku --}}
            <v-form-group :required="true" :error="form.getError('url')">
                {!! Form::label('url', trans("admin/photogalleries/form.labels.url")) !!}
                {!! Form::text('url', null, [
                    'class' => 'form-control maxlength',
                    'maxlength' => '250',
                    ':value' => 'form.url',
                    '@change' => 'onUrlChanged'
                ]) !!}
            </v-form-group>

            <v-form-group :error="form.getError('text')">
                {!! Form::label('text', trans("admin/photogalleries/form.labels.text")) !!}
                {!! Form::textarea('text', null, [
                    'class' => 'form-control',
                    'id' => 'editor-full',
                    ':value' => 'form.text',
                    'rows' => 10
                ]) !!}
            </v-form-group>

            <v-form-group :error="form.getError('sort')">
                {!! Form::label('sort', trans("admin/photogalleries/form.labels.sort")) !!}
                {!! Form::number('sort', null, [
                    'class' => 'form-control',
                    'v-model' => 'form.sort'
                ]) !!}
            </v-form-group>

        </div>

        {{-- PHOTOGALLERY --}}
        <div class="tab-pane has-padding" id="tab_photogallery">
            <photogallery ref="photogallery"
                          :photos="{{ isset($photos) ? $photos->toJson() : '[]' }}"
                          :trans="{{ json_encode(trans('admin/general.photogallery')) }}"
            ></photogallery>
        </div>

        {{-- SEO --}}
        <div class="tab-pane has-padding" id="tab_seo">
            <seo-inputs :title-placeholder="form.title"
                        :form="form"
                        :trans="{{ \App\Helpers\Functions::combineTransToJson([
                            'admin/general.seo', 'admin/photogalleries/form.seo_tab'
                        ]) }}"
            ></seo-inputs>
        </div>

        {{-- OpenGraph --}}
        <div class="tab-pane has-padding" id="tab_og">
            <open-graph-inputs :title-placeholder="form.title"
                               :url-placeholder="form.url"
                               :form="form"
                               :trans="{{ json_encode(trans('admin/general.open_graph')) }}"
            ></open-graph-inputs>
        </div>

        {{-- PLANNING --}}
        <div class="tab-pane has-padding" id="tab_publish">
            <h6 class="panel-title mb-5">{{trans('admin/photogalleries/form.planning_tab.title')}}</h6>
            <p class="mb-15">
                {!! trans('admin/photogalleries/form.planning_tab.help_text') !!}
            </p>

            <div class="form-group {{ $errors->has('publish_at_date') || $errors->has('publish_at_time') ? 'has-error' : '' }}">
                {!! Form::label('publish_at', trans('admin/photogalleries/form.labels.publish_at')) !!}
                <div>
                    <div class="input-group input-group-date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <date-picker class="form-control"
                                     name="{{ $name = 'publish_at_date' }}"
                                     placeholder="{{ trans("admin/photogalleries/form.placeholders.$name") }}"
                                     v-model="form.publish_at_date"
                        ></date-picker>
                        @include('admin.vendor.form.field_error')
                    </div>

                    <div class="input-group input-group-time">
                        <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                        <time-picker class="form-control"
                                     name="{{ $name = 'publish_at_time' }}"
                                     v-model="form.publish_at_time"
                                     placeholder="{{ trans("admin/photogalleries/form.placeholders.$name") }}"
                        ></time-picker>
                        @include('admin.vendor.form.field_error')
                    </div>

                    <br class="clearfix">
                </div>
                <br class="clearfix">
            </div>

            <div class="form-group {{ $errors->has('unpublish_at_date') || $errors->has('unpublish_at_time') ? 'has-error' : '' }}">
                {!! Form::label('unpublish_at', trans('admin/photogalleries/form.labels.unpublish_at')) !!}
                <div>
                    <div class="input-group input-group-date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <date-picker class="form-control"
                                     name="{{ $name = 'unpublish_at_date' }}"
                                     v-model="form.unpublish_at_date"
                                     placeholder="{{ trans("admin/photogalleries/form.placeholders.$name") }}"
                        ></date-picker>
                        @include('admin.vendor.form.field_error')
                    </div>

                    <div class="input-group input-group-time">
                        <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                        <time-picker class="form-control"
                                     name="{{ $name = 'unpublish_at_time' }}"
                                     v-model="form.unpublish_at_time"
                                     placeholder="{{ trans("admin/photogalleries/form.placeholders.$name") }}"
                        ></time-picker>
                        @include('admin.vendor.form.field_error')
                    </div>

                    <br class="clearfix mb10">
                </div>
            </div>

        </div>

    </div>
</div>
