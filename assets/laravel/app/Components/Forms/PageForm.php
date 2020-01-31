<?php

namespace App\Components\Forms;

use App\Helpers\Functions;
use App\Helpers\ViewHelper;
use App\Models\Page\Page;
use App\Models\Web\Language;
use App\Models\Page\Content;
use App\Components\Forms\Templates\AbstractFormWithGridEditor;


class PageForm extends AbstractFormWithGridEditor
{
    /**
     * View name.
     *
     * @var string
     */
    protected $view = 'admin.pages.form';

    /**
     * Language.
     *
     * @var \App\Models\Web\Language
     */
    protected $language;

    /**
     * Page.
     *
     * @var \App\Models\Page\Page
     */
    protected $page;

    /**
     * Use grid editor versions?
     *
     * @var boolean
     */
    protected $useVersions = true;

    /**
     * Widget form.
     *
     * @param \App\Models\Web\Language $language
     * @param \App\Models\Page\Page $page
     * @throws \Exception
     */
    public function __construct(Language $language, Page $page)
    {
        parent::__construct();
        $this->page = $page;
        $this->language = $language;

        $this->addGridEditorScriptsAndStyle();

        $this->addScript(url('plugin/js/pickadate.js'));
        $this->addScript(url('plugin/js/bootstrap-maxlength.js'));
        $this->addScript(mix('js/pages.form.js'));
    }


    /**
     * Get view data.
     *
     * @return array
     */
    public function getViewData(): array
    {
        $data = [
            'languageId' => $this->language->id,
            'page' => $this->page,
            'parentPages' => $this->getParentPages(),
            'views' => $this->getDemarcatedViews(),
            'testingVariantCandidates' => $this->getTestingVariantCandidates(),
            'formValuesJson' => $this->page->getFormAttributesJson([
                'id', 'name', 'url', 'is_homepage', 'published', 'parent_id', 'view', 'image_id',
                'seo_title', 'seo_description', 'seo_index', 'seo_follow', 'seo_sitemap', 'open_graph',
                'publish_at_date', 'publish_at_time', 'unpublish_at_date', 'unpublish_at_time',
            ]),
            'submitUrl' => $this->page->exists ?
                route('admin.pages.update', $this->page->id) :
                route('admin.pages.store'),
        ];

        if ($this->page->exists && $this->page->hasTestingCounterpart()) {
            $testingCounterpart = $this->page->getTestingVariantB();
            $data['testing'] = [
                'defaultValues' => $testingCounterpart->getFormAttributes([
                    'id', 'name', 'view', 'image_id', 'url',
                    'seo_title', 'seo_description', 'seo_index', 'seo_follow', 'seo_sitemap', 'open_graph',
                ]),
                'content' => ($testingCounterpart->getActiveContent() ?: new Content)->getContentAttribute(),
                'versions' => $testingCounterpart->getGridEditorVersions(),
                'versionSwitchUrl' => route('admin.pages.switch_version', $testingCounterpart->getKey()),
                'submitUrl' => route('admin.pages.update', $testingCounterpart->getKey())
            ];
        }

        return $this->getGridEditorData($data);
    }


    /**
     * Get content of the grid editor.
     *
     * @return \App\Models\Interfaces\IsGridEditorContent
     */
    protected function getGridEditorContent(): \App\Models\Interfaces\IsGridEditorContent
    {
        return $this->page->getActiveContent() ?: new Content;
    }


    /**
     * Get versions of the grid editor content.
     *
     * @return array
     */
    protected function getGridEditorVersions(): array
    {
        return $this->page->getGridEditorVersions();
    }


    /**
     * Get versions of the grid editor content.
     *
     * @return string
     */
    protected function getGridEditorVersionSwitchUrl(): string
    {
        if (!$this->page->exists) {
            return "";
        }

        return route('admin.pages.switch_version', $this->page->id);
    }


    /**
     * Get superior pages.
     *
     * @return mixed[]
     */
    private function getParentPages(): array
    {
        $pagesQuery = Page::whereLanguage($this->language)->orderBy('id', 'asc');
        $pagesHierarchy = $pagesQuery->get()->toHierarchy();

        if ($this->page->exists) {
            // TODO: remove children of existing page
        }

        $pages = [];

        foreach ($pagesHierarchy as $page) {
            $this->formatParentPage($page, $pages);
        }

        return array_values($pages);
    }

    private function formatParentPage(Page $page, array &$output): void
    {
        $output[] = [
            'id' => $page->getKey(),
            'name' => $page->name,
            'depth' => $page->depth ?? 0
        ];

        if ($page->children->isNotEmpty()) {
            foreach ($page->children as $childPage) {
                $this->formatParentPage($childPage, $output);
            }
        }
    }


    /**
     * Get demarcated views for pages.
     *
     * @return array
     */
    private function getDemarcatedViews(): array
    {
        return Functions::associativeArrayToSequentialArray(
            ViewHelper::getDemarcatedViews('pages'),
            'key',
            'label',
            'children'
        );
    }

    /**
     * @return mixed[]
     */
    private function getTestingVariantCandidates(): array
    {
        return Page::withoutTestingInvolved()
            ->where('is_homepage', false)
            ->get()
            ->map(static function (Page $page) {
                return [
                    'id' => $page->getKey(),
                    'name' => $page->name
                ];
            })
            ->toArray();
    }
}
