<?php namespace App\Http\Controllers\Admin;

use App\Components\DataTables\RolesTable;
use App\Http\Requests\Admin\RoleRequest;
use App\Models\Entrust\Role;
use App\Components\Forms\RoleForm;
use Illuminate\Http\Request;

class RolesController extends AdminController
{
    /**
     * Active menu item nickname.
     *
     * @var string
     */
    protected $activeMenuItem = 'roles';

    /**
     * RolesController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware('permission:roles-show')->only('index');
        $this->middleware('permission:roles-create')->only(['create', 'store']);
        $this->middleware('permission:roles-edit')->only(['edit', 'update', 'toggle']);
        $this->middleware('permission:roles-delete')->only('delete');
    }

    /**
     * Show roles
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->setTitleDescription(
            trans('admin/roles/general.header_title'), trans('admin/roles/general.descriptions.index')
        );

        $table = new RolesTable($this->getUser());
        return $table->toResponse($request, 'admin.roles.index');
    }


    /**
     * Show form to create new role
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $this->setTitleDescription(
            trans('admin/roles/general.header_title'), trans('admin/roles/general.descriptions.create')
        );

        $form = new RoleForm(new Role([
            'enabled' => true
        ]));
        return $form->getView();
    }


    /**
     * Store new role
     *
     * @param RoleRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(RoleRequest $request)
    {
        $role = new Role($request->getValues());
        $role->setAutomaticName();
        $role->save();

        $role->saveNamedPermissions($request->getPermissions());

        flash(trans('admin/roles/general.notifications.created'), 'success');

        return redirect()->route('admin.roles');
    }


    /**
     * Show form to edit role
     *
     * @param Role $role
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit(Role $role)
    {
        $this->setTitleDescription(
            trans('admin/roles/general.header_title'), trans('admin/roles/general.descriptions.edit')
        );

        if (!$role->isEditable()) {
            flash(trans('admin/roles/general.notifications.protected_role'), 'warning');
            return redirect()->route('admin.roles');
        }

        $form = new RoleForm($role);
        return $form->getView();
    }


    /**
     * Update role
     *
     * @param RoleRequest $request
     * @param Role $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(RoleRequest $request, Role $role)
    {
        if (!$role->isEditable()) {
            flash(trans('admin/roles/general.notifications.protected_role'), 'warning');
            return redirect()->route('admin.roles');
        }

        $role->update($request->getValues());
        $role->saveNamedPermissions($request->getPermissions());

        flash(trans('admin/roles/general.notifications.updated'), 'success');

        return redirect()->route('admin.roles');
    }


    /**
     * Delete role
     *
     * @param Role $role
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function delete(Role $role)
    {
        if (!$role->isEditable()) {
            flash(trans('admin/roles/general.notifications.protected_role'), 'warning');
            return redirect()->route('admin.roles');
        }

        $role->delete();
        flash(trans('admin/roles/general.notifications.deleted'), 'success');

        return $this->refresh();
    }


    /**
     * Toggle enabled / disabled
     *
     * @param Role $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggle(Role $role)
    {
        if (!$role->isEditable()) {
            flash(trans('admin/roles/general.notifications.protected_role'), 'warning');
        } else {
            $role->update([
                'enabled' => !$role->enabled
            ]);
            flash(
                $role->enabled ? trans('admin/roles/general.notifications.enabled') :
                    trans('admin/roles/general.notifications.disabled'),
                'success'
            );
        }

        return $this->refresh();
    }
}
