<?php

namespace App\Components\Forms;

use App\Models\Photogallery\Photogallery;

class PhotogalleryForm extends AbstractForm
{
    /**
     * View name.
     *
     * @var string
     */
    protected $view = 'admin.photogalleries.form';

    /**
     * Photogallery.
     *
     * @var \App\Models\Photogallery\Photogallery
     */
    protected $photogallery;

    /**
     * Photogallery form.
     *
     * @param \App\Models\Photogallery\Photogallery $photogallery
     * @throws \Exception
     */
    public function __construct(Photogallery $photogallery)
    {
        parent::__construct();
        $this->photogallery = $photogallery;
        
        $this->addScript(url('plugin/js/bootstrap-maxlength.js'));
        $this->addScript(url('plugin/js/pickadate.js'));
        $this->addCKEditorScript();

        $this->addScript(mix('js/photogallery.form.js'));
    }


    /**
     * Get view data.
     *
     * @return array
     */
    public function getViewData(): array
    {
        return [
            'photogallery' => $this->photogallery,
            'formDataJson' => $this->photogallery->getFormAttributesJson([
                'title', 'url', 'text', 'sort', 'open_graph',
                'seo_title', 'seo_description', 'seo_index', 'seo_follow', 'seo_sitemap',
                'publish_at_date', 'publish_at_time', 'unpublish_at_date', 'unpublish_at_time'
            ]),
            'submitUrl' => $this->getSubmitUrl(),
            'photos' => $this->photogallery->photos()->with('image')->get()
        ];
    }


    /**
     * Get submit url for form.
     *
     * @return string
     */
    private function getSubmitUrl(): string
    {
        if ($this->photogallery->exists) {
            return route('admin.photogalleries.update', $this->photogallery->getKey());
        } 

        return route('admin.photogalleries.store');
    }
}
