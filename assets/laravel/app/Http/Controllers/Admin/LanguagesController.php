<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\LanguageRequest;
use App\Models\Web\Language;
use App\Services\Settings\Settings;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Components\Forms\LanguageForm;

class LanguagesController extends AdminController
{
    /**
     * Active menu item nickname.
     *
     * @var string
     */
    protected $activeMenuItem = 'languages';
    /**
     * @var \App\Services\Settings\Settings
     */
    private $settings;

    /**
     * LanguagesController constructor.
     * @param \App\Services\Settings\Settings $settings
     */
    public function __construct(Settings $settings)
    {
        parent::__construct();

        $this->middleware('permission:languages-show')->only('index');
        $this->middleware('permission:languages-create')->only([ 'create', 'store' ]);
        $this->middleware('permission:languages-edit')->only([ 'edit', 'update', 'toggleEnabled', 'toggleDefault' ]);
        $this->middleware('permission:languages-delete')->only('delete');
        $this->settings = $settings;
    }


    /**
     * List languages
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $this->setTitleDescription(
            trans('admin/languages/general.header_title'), trans('admin/languages/general.descriptions.index')
        );

        $languages = Language::all();
        $languageDisplay = intval($this->settings->get('language_display', config('admin.language_url.directory')));
        $defaultLanguageHidden = !!$this->settings->get('default_language_hidden', false);

        return view('admin.languages.index', compact('languages', 'defaultLanguageHidden', 'languageDisplay'));
    }


    /**
     * Show form to create new language
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $this->setTitleDescription(
            trans('admin/languages/general.header_title'), trans('admin/languages/general.descriptions.create')
        );

        $form = new LanguageForm(new Language([
            'enabled' => true
        ]));
        return $form->getView();
    }


    /**
     * Store new language
     *
     * @param LanguageRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LanguageRequest $request)
    {
        Language::create($request->getValues());

        flash(trans('admin/languages/general.notifications.created'), 'success');
        return redirect()->route('admin.languages.index');
    }


    /**
     * Show form to edit specified language
     *
     * @param Language $language
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Language $language)
    {
        $this->setTitleDescription(
            trans('admin/languages/general.header_title'), trans('admin/languages/general.descriptions.edit')
        );

        $form = new LanguageForm($language);
        return $form->getView();
    }


    /**
     * Update specified language
     *
     * @param LanguageRequest $request
     * @param Language $language
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(LanguageRequest $request, Language $language)
    {
        $language->update($request->getValues());

        flash(trans('admin/languages/general.notifications.updated'), 'success');
        return redirect()->route('admin.languages.index');
    }


    /**
     * Toggle default specified language
     *
     * @param Language $language
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function toggleDefault(Language $language)
    {
        if(!$language->default) {
            Language::findDefault()->update([
                'default' => 0
            ]);

            $language->update([
                'default' => 1
            ]);
        }

        flash(trans('admin/languages/general.notifications.default', ['name' => $language->name]), 'success');
        return $this->refresh();
    }


    /**
     * Toggle enabled specified language
     *
     * @param Language $language
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function toggleEnabled(Language $language)
    {
        $language->update([
            'enabled' => !$language->enabled
        ]);

        flash(
            $language->enabled ? trans('admin/languages/general.notifications.enabled') :
            trans('admin/languages/general.notifications.disabled'),
            'success'
        );
        return $this->refresh();
    }


    /**
     * Delete specified language
     *
     * @param Language $language
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function delete(Language $language)
    {
        if ($language->default == 1){
            $newDefault = Language::where('enabled', 1)->where('default', 0)->first();
            if(!$newDefault){
                flash(trans('admin/languages/general.notifications.protected_default'), 'warning');
                return $this->refresh();
            }

            $newDefault->default = 1;
            $newDefault->save();
        }

        $language->delete();

        flash(trans('admin/languages/general.notifications.deleted'), 'success');
        return $this->refresh();
    }


    /**
     * Update language settings
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function updateSettings(Request $request) {
        $this->validate($request, [
            'language_display' => [ 'required', Rule::in(config('admin.language_url')) ],
            'default_language_hidden' => 'boolean'
        ]);

        $display = intval($request->input('language_display'));

        $this->settings->put('language_display', $display);

        if ($display === config('admin.language_url.directory')) {
            $this->settings->put(
                'default_language_hidden',
                intval($request->input('default_language_hidden')) === 1
            );
        }

        flash(trans('admin/languages/general.notifications.settings_updated'), 'success');
        return $this->refresh();
    }
}
