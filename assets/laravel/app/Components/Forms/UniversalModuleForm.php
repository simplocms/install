<?php

namespace App\Components\Forms;

use App\Models\UniversalModule\UniversalModuleItem;
use App\Services\UniversalModules\UniversalModule;
use App\Structures\Enums\SingletonEnum;

class UniversalModuleForm extends AbstractForm
{
    /**
     * View name.
     *
     * @var string
     */
    protected $view = 'admin.universalmodule.form';

    /**
     * Prefix.
     *
     * @var string
     */
    protected $prefix;

    /**
     * UniversalModule.
     *
     * @var \App\Services\UniversalModules\UniversalModule
     */
    protected $module;

    /**
     * UniversalModuleItem.
     *
     * @var \App\Models\UniversalModule\UniversalModuleItem
     */
    protected $moduleData;

    /**
     * UniversalModule form.
     *
     * @param string $prefix
     * @param \App\Services\UniversalModules\UniversalModule $module
     * @param \App\Models\UniversalModule\UniversalModuleItem $moduleData
     * @throws \Exception
     */
    public function __construct(string $prefix, UniversalModule $module, UniversalModuleItem $moduleData)
    {
        parent::__construct();
        $this->module = $module;
        $this->prefix = $prefix;
        $this->moduleData = $moduleData;

        $this->addScript(mix('js/universalmodule.form.js'));
    }


    /**
     * Get view data.
     *
     * @return array
     */
    public function getViewData(): array
    {
        return [
            'prefix' => $this->prefix,
            'module' => $this->module,
            'moduleData' => $this->moduleData,
            'submitUrl' => $this->getSubmitUrl(),
            'content' => $this->moduleData->getContent(SingletonEnum::languagesCollection()->getContentLanguage()),
            'itemData' => $this->moduleData->getFormAttributesJson([
                'order', 'enabled', 'url', 'open_graph',
                'seo_title', 'seo_description', 'seo_index', 'seo_follow', 'seo_sitemap'
            ])
        ];
    }


    /**
     * Get submit url for form.
     *
     * @return string
     */
    private function getSubmitUrl(): string
    {
        if ($this->moduleData->exists) {
            return route('admin.universalmodule.update', [
                'prefix' => $this->prefix,
                'id' => $this->moduleData->getKey()
            ]);
        }

        return route('admin.universalmodule.store', $this->prefix);
    }
}
