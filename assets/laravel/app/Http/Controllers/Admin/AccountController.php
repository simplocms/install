<?php namespace App\Http\Controllers\Admin;


use App\Models\User;
use App\Rules\TwitterAccountRule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AccountController extends AdminController
{

    /**
     * Show form to edit role
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit()
    {
        $this->setTitleDescription(
            trans('admin/account/general.title'),
            trans('admin/account/general.description')
        );

        $user = auth()->user();

        $locales = [];
        foreach (config('app.enabled_locales') as $locale) {
            $locales[$locale] = trans('general.language_name', [], $locale);
        }

        return view('admin.account.edit', compact('user', 'locales'));
    }


    /**
     * Update account
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'firstname' => 'max:250',
            'lastname' => 'max:250',
            'email' => ['required', 'email', Rule::unique(User::getTableName(), 'email')->ignore(auth()->id())],
            'username' => ['required', 'max:100', Rule::unique(User::getTableName(), 'username')->ignore(auth()->id())],
            'image' => 'image',
            'position' => 'max:250',
            'about' => 'max:1000',
            'locale' => ['required', Rule::in(config('app.enabled_locales'))],
            'twitter_account' => ['nullable', new TwitterAccountRule]
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $user->update($request->all([
            'firstname', 'lastname', 'email', 'username', 'image', 'position', 'about', 'locale', 'twitter_account'
        ]));

        if ($request->hasFile('image')) {

            if (!file_exists($imagesDir = $user::getImagesDirectory())) {
                mkdir($imagesDir, 0755, true);
            }

            \Image::make($request->file('image'))
                ->encode('jpg')
                ->fit(80, 80)
                ->save($user->image_path);
        } elseif ($request->input('remove_image') == 'true' && $user->hasCustomImage()) {
            \File::delete($user->image_path);
        }

        flash(trans('admin/account/general.notifications.saved', [], $user->locale), 'success');
        return redirect()->route('admin.account.edit');
    }


    /**
     * Change account password
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function changePassword(Request $request)
    {
        $validator = $this->getValidationFactory()->make($request->all(), [
            'password' => 'required',
            'new_password' => 'required|min:8|regex:/^(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/',
            'verify_new_password' => 'required|same:new_password',
        ], trans('admin/account/form.password.messages'));

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (\Hash::check($request->input('password'), $user->password)) {
            $user->forceFill([
                'password' => bcrypt($request->input('new_password'))
            ])->save();
        } else {
            $validator->getMessageBag()->add(
                'password', trans('admin/account/form.password.messages.invalid_password')
            );
            throw new ValidationException($validator);
        }

        flash(trans('admin/account/general.notifications.password_changed'), 'success');
        return redirect()->route('admin.account.edit');
    }
}
