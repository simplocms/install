<?php namespace App\Http\Controllers\Admin;

use App\Components\DataTables\UsersTable;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use App\Components\Forms\UserForm;
use Illuminate\Http\Request;

class UsersController extends AdminController
{
    /**
     * Active menu item nickname.
     *
     * @var string
     */
    protected $activeMenuItem = 'users';

    /**
     * UsersController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware('permission:users-show')->only('index');
        $this->middleware('permission:users-create')->only(['create', 'store']);
        $this->middleware('permission:users-edit')->only(['edit', 'update', 'toggle']);
        $this->middleware('permission:users-delete')->only('delete');
    }

    /**
     * Request: show users
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->setTitleDescription(
            trans('admin/users/general.header_title'), trans('admin/users/general.descriptions.index')
        );

        $table = new UsersTable($this->getUser());
        return $table->toResponse($request, 'admin.users.index');
    }


    /**
     * Request: Show form to create new user
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $this->setTitleDescription(
            trans('admin/users/general.header_title'), trans('admin/users/general.descriptions.create')
        );

        $form = new UserForm(new User([
            'enabled' => true
        ]));
        return $form->getView();
    }


    /**
     * Request: store new user
     *
     * @param UserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserRequest $request)
    {
        $user = User::create($request->getValues());

        $user->roles()->sync($request->getRoles());

        flash(trans('admin/users/general.notifications.created'), 'success');
        return redirect()->route('admin.users');
    }


    /**
     * Request: Show form to update specified user
     *
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(User $user)
    {
        $this->setTitleDescription(
            trans('admin/users/general.header_title'), trans('admin/users/general.descriptions.edit')
        );

        $form = new UserForm($user);
        return $form->getView();
    }


    /**
     * Request: update specified user
     *
     * @param UserRequest $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserRequest $request, User $user)
    {
        if ($user->protected) {
            flash(trans('admin/users/general.notifications.protected_user'), 'warning');
            return redirect()->route('admin.users');
        }

        $user->update($request->getValues());

        $user->roles()->sync($request->getRoles());

        flash(trans('admin/users/general.notifications.updated'), 'success');
        return redirect()->route('admin.users');
    }


    /**
     * Request: delete specified user
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function delete(User $user)
    {
        if ($user->protected) {
            flash(trans('admin/users/general.notifications.protected_user'), 'warning');
            return $this->refresh();
        }

        $user->delete();

        flash(trans('admin/users/general.notifications.deleted'), 'success');
        return $this->refresh();
    }


    /**
     * Request: Toggle user enabled/disabled
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function toggle(User $user)
    {
        if ($user->protected) {
            flash(trans('admin/users/general.notifications.protected_user'), 'warning');
            return $this->refresh();
        }

        $user->update([
            'enabled' => !$user->enabled
        ]);

        flash(
            trans('admin/users/general.notifications.' . ($user->enabled ? 'enabled' : 'disabled')),
            'success'
        );
        return $this->refresh();
    }

}
