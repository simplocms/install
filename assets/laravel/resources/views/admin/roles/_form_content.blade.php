<div class="tabbable tab-content-bordered">
    <ul class="nav nav-tabs nav-tabs-highlight">
        <li class="active">
            <a href="#tab_details" data-toggle="tab">
                {!! trans('admin/roles/form.tabs.details') !!}
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane has-padding active" id="tab_details">

            <div class="form-group required {{ $errors->has($name = 'display_name') ? 'has-error' : '' }}">
                {!! Form::label($name, trans("admin/roles/form.labels.$name")) !!}
                {!! Form::text($name, null, [
                   'class' => 'form-control maxlength',
                    'maxlength' => 100
                ]) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group {{ $errors->has($name = 'description') ? 'has-error' : '' }}">
                {!! Form::label('description', trans("admin/roles/form.labels.$name")) !!}
                {!! Form::text('description', null, [
                    'class' => 'form-control maxlength',
                    'maxlength' => 200
                ]) !!}
                @include('admin.vendor.form.field_error')
            </div>

            <div class="form-group">
                {!! Form::checkbox('enabled', 1, null, ['id' => 'input-enabled']) !!}
                {!! Form::label('input-enabled', trans('admin/roles/form.labels.enabled') ) !!}
            </div>

            <h3>{{ trans('admin/roles/form.permissions_title') }}</h3>

            @foreach($groups as $groupId => $group)
                <table class="table" style="margin-bottom: 15px">
                    <thead>
                    <tr>
                        <th></th>
                        @foreach($group['permissions'] as $permission => $weight)
                            <th>
                                {{ Form::checkbox(null, 1, null, [
                                    'data-all' => $permission == 'all' ? 1 : null
                                ]) }}
                                {{ trans("admin/permissions.permissions.{$permission}") }}
                            </th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($group['areas'] as $area)
                        <tr>
                            <th>{{ trans("admin/permissions.areas.{$area}") }}</th>
                            @foreach($group['permissions'] as $permission => $weight)
                                <td>{{ Form::checkbox("{$area}[{$permission}]", 1, null, [
                                'data-all' => $permission == 'all' ? 1 : null,
                                'checked' => isset($permissionsNames) && isset($permissionsNames["$area-$permission"]) ?: null
                            ]) }}</td>
                            @endforeach
                        </tr>
                    @endforeach

                    @if(isset($group['modules']))
                        @foreach($group['modules'] as $module)
                            <tr>
                                <th>
                                    {{ $module->trans('admin.permissions') }}
                                    <span title="{{ trans('admin/roles/form.title_module') }}">&#9410;</span>
                                </th>
                                @foreach($group['permissions'] as $permission => $weight)
                                    <td>{{ Form::checkbox("module_{$module->getLowerName()}[{$permission}]", 1, null, [
                                    'data-all' => $permission == 'all' ? 1 : null,
                                    'checked' => isset($permissionsNames) && isset($permissionsNames["module_{$module->getLowerName()}-$permission"]) ?: null
                                ]) }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endif

                    @if(isset($group['universal_modules']))
                        <?php /** @var \App\Services\UniversalModules\UniversalModule $uniModule */ ?>
                        @foreach($group['universal_modules'] as $uniModule)
                            <tr>
                                <th>
                                    {{ $uniModule->getName() }}
                                    <span title="{{ trans('admin/roles/form.title_universal_module') }}">&#9418;</span>
                                </th>
                                @foreach($group['permissions'] as $permission => $weight)
                                    <td>{{ Form::checkbox("universal_module_{$uniModule->getKey()}[{$permission}]", 1, null, [
                                    'data-all' => $permission == 'all' ? 1 : null,
                                    'checked' => isset($permissionsNames) && isset($permissionsNames["universal_module_{$uniModule->getKey()}-$permission"]) ?: null
                                ]) }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            @endforeach

        </div>
    </div>
</div>
