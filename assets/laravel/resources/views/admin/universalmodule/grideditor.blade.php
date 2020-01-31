<?php /** @var \App\Models\UniversalModule\UniversalModuleItem[] $items */ ?>
<div id="universal-module-{{ $entity->prefix }}-form">
    <v-form action="{{ route('admin.universalmodule.validateAndPreview') }}" :form="form">
        {{-- View --}}
        <v-form-group :error="form.getError('view')" :required="true">
            <label for="universal-module-view-select">
                {{ trans('admin/universal_modules.grid_editor_form.label_view') }}
            </label>
            <multiselect name="view"
                         v-model="form.view"
                         :options="{{ json_encode($views) }}"
                         label="label"
                         track-by="key"
                         group-label="label"
                         group-values="children"
                         :allow-empty="false"
            ></multiselect>
        </v-form-group>

        {{-- All items --}}
        <v-checkbox-switch name="all_items" v-model="form.all_items">
            {{ trans('admin/universal_modules.grid_editor_form.label_all_items') }}
        </v-checkbox-switch>

        {{-- Items --}}
        <v-form-group :error="form.getError('items')" v-show="!form.all_items" :required="true">
            <label for="universal-module-items-select">
                {{ trans('admin/universal_modules.grid_editor_form.label_items') }}
            </label>

            <multiselect name="items[]"
                         v-model="form.items"
                         :options="{{ json_encode($items) }}"
                         :multiple="true"
                         label="name"
                         track-by="id"
            ></multiselect>
        </v-form-group>
    </v-form>
</div>

<script>
    window.universalModuleOptions = function () {
        return {
            model: {!! json_encode($model) !!},
        };
    };
</script>
{!! Html::script(mix('js/universalmodule.ge-form.js')) !!}
