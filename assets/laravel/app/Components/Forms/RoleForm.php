<?php

namespace App\Components\Forms;

use App\Models\Entrust\Role;
use App\Models\Module\InstalledModule;
use App\Structures\Enums\SingletonEnum;

class RoleForm extends AbstractForm
{
    /**
     * View name.
     *
     * @var string
     */
    protected $view = 'admin.roles.form';

    /**
     * Role.
     *
     * @var \App\Models\Entrust\Role
     */
    protected $role;

    /**
     * Role form.
     *
     * @param \App\Models\Entrust\Role $role
     * @throws \Exception
     */
    public function __construct(Role $role)
    {
        parent::__construct();
        $this->role = $role;

        $this->addScript(url('plugin/js/switchery.js'));
        $this->addScript(url('plugin/js/bootstrap-maxlength.js'));

        $this->addScript(mix('js/roles.form.js'));
    }


    /**
     * Get view data.
     *
     * @return array
     */
    public function getViewData(): array
    {
        return [
            'role' => $this->role,
            'permissionsNames' => $this->role->exists ? $this->role->getAllPermissionsNames() : null,
            'groups' => $this->getPermissionGroups(),
            'submitUrl' => $this->getSubmitUrl()
        ];
    }


    /**
     * Get submit url for form.
     *
     * @return string
     */
    private function getSubmitUrl(): string
    {
        if ($this->role->exists) {
            return route('admin.roles.update', $this->role->id);
        }

        return route('admin.roles.store');
    }


    /**
     * Load permission groups including modules.
     *
     * @return array
     */
    private function getPermissionGroups(): array
    {
        $groups = config('permissions.groups', []);

        /** @var \App\Models\Module\InstalledModule $installedModule */
        foreach (InstalledModule::enabled()->get() as $installedModule) {
            if (!$installedModule->module) {
                continue;
            }

            $moduleGroup = $installedModule->module->config('admin.permissions.group');

            if ($moduleGroup && isset($groups[$moduleGroup])) {
                $groups[$moduleGroup]['modules'][] = $installedModule->module;
            }
        }

        foreach (SingletonEnum::universalModules()->all() as $module) {
            $groups[1]['universal_modules'][] = $module;
        }

        return $groups;
    }
}
