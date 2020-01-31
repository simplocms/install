<?php

namespace App\Http\Controllers\Admin;

use App\Components\DataTables\RedirectsTable;
use App\Components\Forms\RedirectForm;
use App\Http\Requests\Admin\RedirectBulkCreateRequest;
use App\Http\Requests\Admin\RedirectRequest;
use App\Models\Web\Language;
use App\Models\Web\Redirect;
use App\Structures\Enums\SingletonEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RedirectsController extends AdminController
{
    /**
     * Active menu item nickname.
     *
     * @var string
     */
    protected $activeMenuItem = 'redirects';

    /**
     * RedirectsController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:redirects-show')->only('index');
        $this->middleware('permission:redirects-create')->only(['create', 'store']);
        $this->middleware('permission:redirects-edit')->only(['edit', 'update']);
        $this->middleware('permission:redirects-delete')->only('delete');
    }


    /**
     * Request: Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->setTitleDescription(
            trans('admin/redirects/general.header_title'),
            trans('admin/redirects/general.descriptions.index')
        );

        $table = new RedirectsTable($this->getUser());
        return $table->toResponse($request, 'admin.redirects.index');
    }


    /**
     * Request: Show the form for creating a new redirect.
     *
     * @return \App\Components\Forms\RedirectForm
     * @throws \Exception
     */
    public function create()
    {
        $this->setTitleDescription(
            trans('admin/redirects/general.header_title'),
            trans('admin/redirects/general.descriptions.create')
        );

        return new RedirectForm(new Redirect([
            'status_code' => 301
        ]));
    }


    /**
     * POST: Store new redirect.
     *
     * @param \App\Http\Requests\Admin\RedirectRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(RedirectRequest $request)
    {
        // Create redirect
        $redirect = new Redirect($request->getValues());
        $redirect->user_id = auth()->id();
        $redirect->save();

        flash(trans('admin/redirects/general.notifications.created'), 'success');
        return $this->redirect(route('admin.redirects.index'));
    }


    /**
     * Request: Show the form for editing the specified redirect.
     *
     * @param \App\Models\Web\Redirect $redirect
     * @return \App\Components\Forms\RedirectForm
     * @throws \Exception
     */
    public function edit(Redirect $redirect)
    {
        $this->setTitleDescription(
            trans('admin/redirects/general.header_title'),
            trans('admin/redirects/general.descriptions.edit')
        );

        return new RedirectForm($redirect);
    }


    /**
     * PUT: Update the specified redirect.
     *
     * @param \App\Http\Requests\Admin\RedirectRequest $request
     * @param \App\Models\Web\Redirect $redirect
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(RedirectRequest $request, Redirect $redirect)
    {
        $redirect->fill($request->getValues())->save();

        flash(trans('admin/redirects/general.notifications.updated'), 'success');
        return $this->redirect(route('admin.redirects.index'));
    }


    /**
     * DELETE: delete specified redirect.
     *
     * @param \App\Models\Web\Redirect $redirect
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function delete(Redirect $redirect)
    {
        $redirect->delete();
        flash(trans('admin/redirects/general.notifications.deleted'), 'success');
        return $this->refresh();
    }


    /**
     * GET: export existing redirects.
     *
     * @return \Illuminate\Http\Response
     */
    public function export()
    {
        return $this->getCsvResponse(Redirect::orderBy('from')->get());
    }


    /**
     * GET: import example.
     *
     * @return \Illuminate\Http\Response
     */
    public function importExample()
    {
        return $this->getCsvResponse(collect([]), 'import.csv');
    }


    /**
     * Request: Show the form for creating a new redirect.
     *
     * @return \App\Components\Forms\RedirectForm
     * @throws \Exception
     */
    public function bulkCreate()
    {
        $this->setTitleDescription(
            trans('admin/redirects/general.header_title'),
            trans('admin/redirects/general.descriptions.bulk_create')
        );

        $statusCodes = [
            301 => trans('admin/redirects/form.status_codes.301'),
            302 => trans('admin/redirects/form.status_codes.302'),
            307 => trans('admin/redirects/form.status_codes.307'),
            308 => trans('admin/redirects/form.status_codes.308'),
        ];

        return view('admin.redirects.bulk_create_form', compact('statusCodes'));
    }


    /**
     * POST: bulk store redirects.
     *
     * @param \App\Http\Requests\Admin\RedirectBulkCreateRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function bulkStore(RedirectBulkCreateRequest $request)
    {
        array_map(function (array $data) {
            $redirect = new Redirect($data);
            $redirect->user_id = auth()->id();
            return $redirect->save();
        }, $request->getRedirects());

        flash(trans('admin/redirects/general.notifications.bulk_created'), 'success');
        return $this->redirect(route('admin.redirects.index'));
    }


    /**
     * Get CSV file download response.
     *
     * @param \Illuminate\Support\Collection|\App\Models\Web\Redirect[] $redirects
     * @param string $filename
     * @return \Illuminate\Http\Response
     */
    private function getCsvResponse(Collection $redirects, string $filename = 'redirects.csv')
    {
        $separator = ';';
        $csv[] = join($separator, ['Source', 'Target', 'Status code']);

        foreach ($redirects as $redirect) {
            $csv[] = join($separator, [$redirect->from, $redirect->to, $redirect->status_code]);
        }

        return response(join(PHP_EOL, $csv), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
