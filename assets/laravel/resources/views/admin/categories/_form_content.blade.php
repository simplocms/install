<div class="tabbable tab-content-bordered" id="categories-form">
    <ul class="nav nav-tabs nav-tabs-highlight">
        <li class="active">
            <a href="#tab_details" data-toggle="tab" aria-expanded="true">
                {{ trans('admin/category/form.tabs.info') }}
            </a>
        </li>
        <li>
            <a href="#tab_seo" data-toggle="tab" aria-expanded="false">
                {{ trans('admin/category/form.tabs.seo') }}
            </a>
        </li>
        <li>
            <a href="#tab_og" data-toggle="tab" aria-expanded="false">
                {{ trans('admin/category/form.tabs.og') }}
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane has-padding active" id="tab_details">
            {{-- Category name --}}
            <v-form-group :required="true" :error="form.getError('name')">
                {!! Form::label('name', trans("admin/category/form.labels.name")) !!}
                {!! Form::text('name', null, [
                    'class' => 'form-control maxlength',
                    'maxlength' => '150',
                    ':value' => 'form.name',
                    '@change' => 'onNameChanged'
                ]) !!}
            </v-form-group>

            {{-- Category URL slug --}}
            <v-form-group :required="true" :error="form.getError('url')">
                {!! Form::label('url', trans("admin/category/form.labels.url")) !!}
                {!! Form::text('url', null, [
                    'class' => 'form-control maxlength',
                    'maxlength' => '100',
                    ':value' => 'form.url',
                    '@change' => 'onUrlChanged'
                ]) !!}
            </v-form-group>

            {{-- Category description --}}
            <v-form-group :error="form.getError('description')">
                {!! Form::label('description', trans("admin/category/form.labels.description")) !!}
                {!! Form::textarea('description', null, [
                    'class' => 'form-control',
                    'maxlength' => '1000',
                    'v-model' => 'form.description',
                    'v-maxlength',
                    'rows' => 6
                ]) !!}
            </v-form-group>

            {{-- Category parent --}}
            <v-form-group :error="form.getError('parent_id')">
                {!! Form::label('parent_id', trans('admin/category/form.labels.parent_id')) !!}
                <select name="parent_id" class="form-control" v-model="form.parent_id">
                    <option :value="null">{{ trans('admin/category/form.default_parent_category') }}</option>

                    @foreach($categories as $item)
                        @include('admin.categories._nested', $item)
                    @endforeach

                </select>
            </v-form-group>

            {{-- Category show public --}}
            <v-checkbox-switch v-model="form.show" name="show">
                {{ trans('admin/category/form.labels.show') }}
            </v-checkbox-switch>
        </div>

        <div class="tab-pane has-padding" id="tab_seo">
            <seo-inputs :title-placeholder="form.name"
                        :form="form"
                        :trans="{{ \App\Helpers\Functions::combineTransToJson([
                            'admin/general.seo', 'admin/category/form.seo_tab'
                        ]) }}"
            ></seo-inputs>
        </div>

        <div class="tab-pane has-padding" id="tab_og">
            <open-graph-inputs :title-placeholder="form.name"
                               :url-placeholder="form.url"
                               :form="form"
                               :trans="{{ json_encode(trans('admin/general.open_graph')) }}"
            ></open-graph-inputs>
        </div>

    </div>
</div>
