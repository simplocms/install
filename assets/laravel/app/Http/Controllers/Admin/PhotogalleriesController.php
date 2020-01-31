<?php

namespace App\Http\Controllers\Admin;

use App\Components\DataTables\PhotogalleriesTable;
use App\Http\Requests\Admin\PhotogalleryRequest;
use App\Models\Photogallery\Photogallery;
use App\Components\Forms\PhotogalleryForm;
use Illuminate\Http\Request;

class PhotogalleriesController extends AdminController
{
    /**
     * Active menu item nickname.
     *
     * @var string
     */
    protected $activeMenuItem = 'photogalleries';

    /**
     * PhotogalleriesController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:photogalleries-show')
            ->only(['index']);

        $this->middleware('permission:photogalleries-create')
            ->only(['create', 'store']);

        $this->middleware('permission:photogalleries-edit')
            ->only(['edit', 'update']);

        $this->middleware('permission:photogalleries-edit|photogalleries-create')
            ->only(['updatePhoto', 'photoList', 'deletePhoto']);

        $this->middleware('permission:photogalleries-delete')
            ->only('delete');
    }


    /**
     * Request: List of photogalleries
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->setTitleDescription(
            trans('admin/photogalleries/general.header_title'),
            trans('admin/photogalleries/general.descriptions.index')
        );

        $table = new PhotogalleriesTable($this->getLanguage(), $this->getUser());
        return $table->toResponse($request, 'admin.photogalleries.index');
    }


    /**
     * GET: Create new photogallery.
     *
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function create()
    {
        $this->setTitleDescription(
            trans('admin/photogalleries/general.header_title'),
            trans('admin/photogalleries/general.descriptions.create')
        );

        $form = new PhotogalleryForm(
            new Photogallery([
                'publish_at' => \Carbon\Carbon::now(),
                'sort' => (Photogallery::query()->max('sort') ?: 0) + 1,
                'seo_index' => true,
                'seo_follow' => true,
                'seo_sitemap' => true,
            ])
        );
        return $form->getView();
    }


    /**
     * GET: Update photogallery.
     *
     * @param \App\Models\Photogallery\Photogallery $photogallery
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function edit(Photogallery $photogallery)
    {
        // redirect when photogallery language does not match.
        $this->redirectWhenLanguageNotMatch($photogallery, 'admin.photogalleries');
        $this->setTitleDescription(
            trans('admin/photogalleries/general.header_title'),
            trans('admin/photogalleries/general.descriptions.edit')
        );

        $form = new PhotogalleryForm($photogallery);
        return $form->getView();
    }


    /**
     * POST: Store new photogallery
     *
     * @param \App\Http\Requests\Admin\PhotogalleryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PhotogalleryRequest $request)
    {
        // Create photogallery
        $photogallery = new Photogallery($request->getValues());
        $photogallery->setLanguage($this->getLanguage());
        $photogallery->user_id = auth()->id();
        $photogallery->save();

        $photogallery->savePhotogallery($request->getPhotogallery());

        flash(trans('admin/photogalleries/general.notifications.created'), 'success');
        return $this->redirect(route('admin.photogalleries'));
    }


    /**
     * POST: Update photogallery
     *
     * @param \App\Http\Requests\Admin\PhotogalleryRequest $request
     * @param \App\Models\Photogallery\Photogallery $photogallery
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PhotogalleryRequest $request, Photogallery $photogallery)
    {
        // Save values
        $photogallery->update($request->getValues());
        $photogallery->savePhotogallery($request->getPhotogallery());

        flash(trans('admin/photogalleries/general.notifications.updated'), 'success');
        return $this->redirect(route('admin.photogalleries'));
    }


    /**
     * DELETE: delete photogallery
     *
     * @param \App\Models\Photogallery\Photogallery $photogallery
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function delete(Photogallery $photogallery)
    {
        $photogallery->delete();

        flash(trans('admin/photogalleries/general.notifications.deleted'), 'success');
        return $this->refresh();
    }
}
