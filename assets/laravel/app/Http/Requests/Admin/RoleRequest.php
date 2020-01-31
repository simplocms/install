<?php

namespace App\Http\Requests\Admin;

use App\Models\Module\InstalledModule;
use App\Structures\Enums\SingletonEnum;
use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'display_name' => 'required|max:100',
            'description' => 'max:200'
        ];
    }


    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return trans('admin/roles/form.messages');
    }


    /**
     * Return values
     *
     * @return array
     */
    public function getValues()
    {
        $input = $this->all(['display_name', 'description', 'enabled']);
        $input['enabled'] = isset($input['enabled']);
        return $input;
    }


    /**
     * Return permissions
     *
     * @return array
     */
    public function getPermissions()
    {
        $output = [];
        $groups = config('permissions.groups');

        // System permissions //

        foreach($groups as $groupId => $group){

            $permissions = $group['permissions'];

            foreach($group['areas'] as $area){
                $highestPermissions = $this->getAreaHighestPermissions($area, $permissions);

                if($highestPermissions) {
                    $output = array_merge($output, $highestPermissions);
                }
            }
        }

        // Modules //

        /** @var InstalledModule $installedModule */
        foreach (InstalledModule::enabled()->get() as $installedModule) {
            if (!$installedModule->module) {
                continue;
            }

            $moduleGroup = $installedModule->module->config('admin.permissions.group');

            if ($moduleGroup && isset($groups[$moduleGroup])) {
                $highestPermissions = $this->getAreaHighestPermissions(
                    "module_{$installedModule->module->getLowerName()}",
                    $groups[$moduleGroup]['permissions']
                );

                if($highestPermissions) {
                    $output = array_merge($output, $highestPermissions);
                }
            }
        }

        // Universal modules //

        foreach (SingletonEnum::universalModules()->all() as $uniModule) {
            $moduleGroup = 1;

            $highestPermissions = $this->getAreaHighestPermissions(
                "universal_module_{$uniModule->getKey()}",
                $groups[$moduleGroup]['permissions']
            );

            if($highestPermissions) {
                $output = array_merge($output, $highestPermissions);
            }
        }

        return $output;
    }


    /**
     * Get only highest permissions for specified area.
     *
     * @param string $area
     * @param array $permissions
     * @return array|bool
     */
    private function getAreaHighestPermissions($area, $permissions) {

        $areaPermissions = $this->input($area);
        if(!$areaPermissions) return false;

        $highestPermissions = [];
        $highestWeight = 0;

        foreach($areaPermissions as $areaPermission => $_){
            if(!isset($permissions[$areaPermission])) continue;
            $weight = $permissions[$areaPermission];

            if($weight > $highestWeight){
                $highestPermissions = [ "$area-$areaPermission" ];
                $highestWeight = $weight;
            }
            elseif($weight == $highestWeight){
                $highestPermissions[] = "$area-$areaPermission";
            }
        }

        return $highestPermissions ?: false;
    }
}
