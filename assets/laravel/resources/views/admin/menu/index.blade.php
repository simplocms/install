@extends('admin.layouts.master')
@section('content')
    <?php
    $canEdit = auth()->user()->can('menu-edit');
    ?>
    <menu-page inline-template
               v-cloak
               :trans="{{ json_encode(trans('admin/menu') + ['flash_level' => trans('admin/general.flash_level')]) }}"
    >
        <div id="menu-page">
            <div class="row">
                @permission('menu-edit')
                <div class="col-md-3" id="menu-content-creator">
                    <div class="tabbable tab-content-bordered">
                        <ul class="nav nav-tabs nav-tabs-highlight">
                            <li class="active">
                                <a href="#tab_tree" data-toggle="tab">
                                    {{trans('admin/menu.tabs.pages')}}
                                </a>
                            </li>
                            <li>
                                <a href="#tab_url" data-toggle="tab">
                                    {{trans('admin/menu.tabs.custom_pages')}}
                                </a>
                            </li>
                            <li>
                                <a href="#tab_categories" data-toggle="tab">
                                    {{trans('admin/menu.tabs.categories')}}
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">

                            <div class="tab-pane has-padding active" id="tab_tree">
                                <div id="page-tree"></div>
                                <br>
                                <button class="btn bg-teal-400" @click="addSelectedPages">
                                    {{trans('admin/menu.buttons.add_pages')}}
                                </button>
                                <div class="clearfix"></div>
                            </div>

                            <div class="tab-pane has-padding" id="tab_url">

                                <div class="form-group">
                                    {{ Form::label('custom_page_name', trans('admin/menu.custom_page_labels.custom_page_name'), [
                                        'class' => 'form-label'
                                    ]) }}
                                    {{ Form::text('custom_page_name', null, [
                                        'class' => 'form-control',
                                        'v-model' => 'customPage.name'
                                    ]) }}
                                </div>

                                <div class="form-group">
                                    {{ Form::label('custom_page_url', trans('admin/menu.custom_page_labels.custom_page_url'), [
                                        'class' => 'form-label'
                                    ]) }}
                                    {{ Form::text('custom_page_url', null, [
                                        'class' => 'form-control',
                                        'placeholder' => 'https://',
                                        'v-model' => 'customPage.url'
                                    ]) }}
                                </div>

                                <button class="btn bg-teal-400" @click="addCustomPage">
                                    {{trans('admin/menu.buttons.add_custom_page')}}
                                </button>
                            </div>

                            <div class="tab-pane has-padding" id="tab_categories">
                                {{ Form::select(null, $flags, null, [
                                    'class' => 'form-control',
                                    'ref' => 'categoryFlagSelect',
                                    'v-on:change' => 'loadAndInitCategoryTree'
                                ]) }}
                                <br>
                                <div id="category-tree"></div>
                                <br>
                                <button class="btn bg-teal-400" @click="addSelectedCategories">
                                    {{trans('admin/menu.buttons.add_categories')}}
                                </button>
                                <div class="clearfix"></div>
                            </div>

                        </div>
                    </div>
                </div>
                @endpermission

                <div class="col-md-{{ $canEdit ? 9 : 12 }}">
                    {{-- Create menu and switch menu --}}
                    <div class="panel panel-flat">
                        <div class="panel-body">

                            <div class="form-group mb-5">
                                <multiselect v-model="activeMenuId"
                                             :options="menuList"
                                             label="name"
                                             track-by="id"
                                             :allow-empty="false"
                                             :preselect-first="true"
                                             placeholder="{{ trans('admin/pages/form.placeholders.view') }}"
                                ></multiselect>
                            </div>

                            @permission('menu-create')
                            <button class="btn bg-primary btn-labeled pull-right" @click="newMenuModal"
                                    id="create-new-menu-button">
                                <b class="fa fa-trello"></b>
                                {{trans('admin/menu.buttons.create')}}
                            </button>
                            @endpermission

                        </div>
                    </div>

                    {{-- Menu fields --}}
                    <div class="panel panel-flat" v-show="activeMenu" style="display:none" id="menu-content-panel">
                        <div class="panel-body" v-if="activeMenu">
                            <h4>@{{ activeMenu.name }}</h4>

                            <ol class="dd-list" id="menu-items-list" v-if="renderItems">
                                <drop-zone :target="activeMenu.items"></drop-zone>
                                <template v-for="(menuItem, index) in activeMenu.items">
                                    <menu-item :item="menuItem"
                                               v-on:remove="removeItem(index)"
                                               :index="index"
                                               :localization="localization"
                                               :key="index"
                                               :source-list="activeMenu.items"
                                               :path="index.toString()"
                                               :max-depth="{{ $maxDepth ?? 0 }}"
                                    ></menu-item>
                                    <drop-zone :target="activeMenu.items"
                                               :position="index + 1"
                                    ></drop-zone>
                                </template>
                            </ol>

                            <em v-show="!activeMenu.items.length">
                                {{ trans('admin/menu.empty_menu') }}
                            </em>

                            {{-- SAVE OR DELETE --}}
                            @permission('menu-delete')
                            <div class="row">
                                <div class="col-xs-12 mt-15">
                                    <v-confirm-action action="emit"
                                                      id="delete-menu-button"
                                                      :texts="trans.confirm_delete"
                                                      :danger-mode="true"
                                                      class="text-danger pull-right"
                                                      @confirm="deleteMenu('{{ route('admin.menu.delete') }}')"
                                    >
                                        {{ trans('admin/menu.buttons.delete') }}
                                    </v-confirm-action>
                                </div>
                            </div>
                            @endpermission
                        </div>
                    </div>

                    {{-- Locations settings --}}
                    @if ($menuLocations)
                    <div class="panel panel-flat">
                        <div class="panel-body">
                            <h4>
                                {{ trans('admin/menu.settings.title') }} -
                                {{ trans('admin/menu.settings.text_template_location') }}
                            </h4>

                            @foreach($menuLocations as $menu => $text)
                            <div class="form-group">
                                {{ Form::label("locations-$menu-menu", trans($text)) }}

                                <select id="locations-{{$menu}}-menu"
                                        class="form-control"
                                        v-model="menuLocations.{{ $menu }}"
                                >
                                    <option value="null">{{ trans('admin/menu.default_menu_for_position') }}</option>
                                    <option v-for="(menuComponent, index) in menuList"
                                            :value="menuComponent.id"
                                            :key="menuComponent.id"
                                    >
                                        @{{ menuComponent.name }}
                                    </option>
                                </select>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- SAVE OR DELETE --}}
                    @permission('menu-edit')
                    <div class="row">
                        <div class="col-xs-12">
                            <button class="btn bg-teal-400 btn-labeled" @click="saveChanges"
                                    id="save-menu-button">
                                <b class="fa fa-floppy-o"></b> {{ trans('admin/menu.buttons.save') }}
                            </button>
                        </div>
                    </div>
                    @endpermission
                </div>
            </div>

            @permission('menu-create')
            <!-- Modal -->
            <div id="add-menu-modal" class="modal fade in">
                <div class="modal-dialog">
                    <div class="modal-content">

                        {{--Modal header--}}
                        <div class="modal-header">
                            <button class="close" type="button" data-dismiss="modal">
                                <span aria-hidden="true">×</span>
                            </button>
                            <h4 class="modal-title">{{ trans('admin/menu.create_modal.title') }}</h4>
                        </div>

                        {{-- Modal body --}}
                        <div class="modal-body">

                            <div class="form-group">
                                {{ Form::label('name', trans('admin/menu.create_modal.label_name'), [
                                    'class' => 'control-label'
                                ]) }}
                                {{ Form::text('name', null, [
                                    'class' => 'form-control maxlength',
                                    'maxlength' => '250',
                                    'v-model' => 'newMenu.name'
                                ]) }}
                            </div>

                        </div>

                        {{-- Modal footer --}}
                        <div class="modal-footer">
                            <button class="btn btn-default pull-left" type="button" data-dismiss="modal">
                                {{trans('admin/menu.create_modal.btn_cancel')}}
                            </button>
                            <button class="btn btn-primary" type="button" @click="createNewMenu">
                                {{trans('admin/menu.create_modal.btn_create')}}
                            </button>
                        </div>

                    </div>
                </div>
            </div>
            @endpermission
        </div>
    </menu-page>
@endsection

@push('script')
    {{ Html::script(url('plugin/js/jquery-ui.js')) }}
    {{ Html::script(url('plugin/js/fancytree.js')) }}

    <script>
        window.menuPageOptions = function () {
            return {
                urls: {
                    newMenu: "{{ route('admin.menu.store') }}",
                    saveMenu: "{{ route('admin.menu.update') }}",
                    pageTree: "{{ url(route('admin.menu.pages')) }}",
                    categoryTree: "{{ url(route('admin.menu.categories')) }}"
                },
                menuList: {!! json_encode($menuList) !!},
                menuLocations: {!! json_encode($defaultTheme->menu_locations) !!},
                canEdit: {{ $canEdit ? 'true' : 'false' }}
            };
        }
    </script>

    {{ Html::script( url('js/menu.page.js') ) }}
@endpush
