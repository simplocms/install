<?php

namespace App\Components\Forms;

use App\Models\User;

class UserForm extends AbstractForm
{
    /**
     * View name.
     *
     * @var string
     */
    protected $view = 'admin.users.form';

    /**
     * User.
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * User form.
     *
     * @param \App\Models\User $user
     */
    public function __construct(User $user)
    {
        parent::__construct();
        $this->user = $user;
        
        $this->addScript(url('plugin/js/switchery.js'));
        $this->addScript(url('plugin/js/bootstrap-maxlength.js'));

        $this->addScript(mix('js/users.form.js'));
    }


    /**
     * Get view data.
     *
     * @return array
     */
    public function getViewData(): array
    {
        return [
            'user' => $this->user,
            'roles' => $this->getRoles(),
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
        if ($this->user->exists) {
            return route('admin.users.update', $this->user->id);
        } 

        return route('admin.users.store');
    }


    /**
     * Get roles.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getRoles()
    {
        $roles = \App\Models\Entrust\Role::enabled();

        if (!auth()->user()->isAdmin()) {
            $roles->whereNotIn('name', ['programmer', 'administrator']);
        }

        return $roles->get();
    }
}
