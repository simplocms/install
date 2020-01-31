<?php

namespace App\Services\Pages;

use App\Exceptions\GridEditorException;
use App\Models\Page\Content;
use App\Models\Page\Page;
use App\Models\Web\Language;
use Illuminate\Support\Facades\DB;

final class PageManager
{
    /**
     * @var \App\Services\Pages\PageABTestingManager
     */
    private $ABTestingManager;

    /**
     * @param \App\Services\Pages\PageABTestingManager $ABTestingManager
     */
    public function __construct(PageABTestingManager $ABTestingManager)
    {
        $this->ABTestingManager = $ABTestingManager;
    }

    /**
     * @param \App\Models\Page\Page $page
     * @param mixed[] $data
     */
    public function update(Page $page, array $data): void
    {
        $parentId = $data['parent_id'] ?? null;
        unset($data['parent_id']);

        // Fill model with input.
        if ($page->isTestingCounterpart()) {
            $this->ABTestingManager->updateTestingCounterpart($page, $data);
        } elseif ($page->hasTestingCounterpart()) {
            $this->ABTestingManager->updateTestedPage($page, $data);
        } else {
            $page->fill($data)->save();
        }

        if (!$page->isTestingCounterpart()) {
            // Update parent page.
            if ($parentId) {
                $page->makeChildOf(Page::query()->find($parentId));
            } else {
                $page->makeRoot();
            }

            $page->save();
        }
    }


    /**
     * @param \App\Models\Page\Page $page
     * @param int $contentId
     * @param mixed[]|null $newContent
     * @return \App\Models\Page\Content
     * @throws \App\Exceptions\GridEditorException
     */
    public function updateActivePageContent(Page $page, int $contentId, ?array $newContent = null): Content
    {
        // Set active content before content is examined for changes.
        /** @var \App\Models\Page\Content $activeContent */
        $activeContent = $page->contents()->findOrFail($contentId);
        $activeContent->setActive();

        // Examine content for changes and save it with modules.
        /** @var \App\Models\Page\Content $newVersion */
        $newVersion = $page->createNewVersionIfChanged($newContent);

        return $newVersion ?? $activeContent;
    }

    /**
     * @param \App\Models\Web\Language $language
     * @param mixed[] $data
     * @param mixed[]|null $content
     * @return \App\Models\Page\Page
     * @throws \App\Exceptions\GridEditorException
     */
    public function create(Language $language, array $data, ?array $content = null): Page
    {
        // Create page
        $page = new Page($data);
        $page->setLanguage($language);
        $page->save();

        // Save content.
        try {
            $page->createNewVersionIfChanged($content);
        } catch (GridEditorException $e) {
            $page->forceDelete();
            throw $e;
        }

        // Set parent page.
        if ($page->parent_id) {
            $page->makeChildOf($page->parent);
        }

        $page->save();
        return $page;
    }

    /**
     * @param \App\Models\Page\Page $page
     */
    public function delete(Page $page): void
    {
        DB::transaction(function () use ($page) {
            if ($page->hasTestingCounterpart()) {
                $this->ABTestingManager->deleteVariantB($page->testing_b_id);
            }

            $page->delete();
        });
    }

    /**
     * @param \App\Models\Page\Page $page
     */
    public function setUpABTesting(Page $page): void
    {
        $this->ABTestingManager->setUp($page);
    }

    /**
     * @param \App\Models\Page\Page $page
     * @param string|null $keep
     */
    public function stopABTesting(Page $page, ?string $keep = null): void
    {
        if ($keep && strtolower($keep) === 'a') {
            $this->ABTestingManager->stopAndKeepA($page);
        } elseif ($keep && strtolower($keep) === 'b') {
            $this->ABTestingManager->stopAndKeepB($page);
        } else {
            $this->ABTestingManager->stopAndKeepBoth($page);
        }
    }
}
