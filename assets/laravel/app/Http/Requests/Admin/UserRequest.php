<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Entrust\Role;

class UserRequest extends FormRequest
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
        $rules = [
            'firstname' => 'required|max:50',
            'lastname' => 'required|max:50',
            'username' => ['required', 'max:50'],
            'email' => ['required', 'email'],
            'role.*' => [
                Rule::exists(Role::getTableName(), 'id')
            ]
        ];

        // Creating new
        if ($this->route()->getName() === "admin.users.store") {
            $rules['email'][] = Rule::unique('users', 'email');
            $rules['username'][] = Rule::unique('users', 'username');
            $rules['password'] = 'required|min:6|confirmed';
        } else {
            $rules['email'][] = Rule::unique('users', 'email')
                ->ignore($this->route('user')->id);

            $rules['username'][] = Rule::unique('users', 'username')
                ->ignore($this->route('user')->id);
        }

        return $rules;
    }


    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return trans('admin/users/form.messages');
    }


    /**
     * Get all of the input and files for the request.
     *
     * @return array
     */
    public function getValues()
    {
        $all = $this->all([
            'firstname', 'lastname', 'username', 'email', 'enabled'
        ]);

        $password = $this->input('password');
        if (!is_null($password) && strlen($password)) {
            $all['password'] = \Hash::make($password);
        }

        $all['enabled'] = intval($all['enabled'] ?? 0) ? 1 : 0;
        return $all;
    }


    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->role ?: [];
    }
}
