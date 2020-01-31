<?php

namespace App\Services\Pages;

use App\Models\Page\Page;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

final class PageABTestingManager
{
    /**
     * @param \App\Models\Page\Page $variantA
     */
    public function setUp(Page $variantA): void
    {
        DB::transaction(function () use ($variantA) {
            $variantB = $variantA->replicateFull([
                'name' => $bName = $variantA->name . ' - B',
                'url' => $variantA->is_homepage ? $bName : $variantA->url . '-b',
                'testing_a_id' => null,
                'testing_b_id' => null,
            ]);

            $this->activate($variantA, $variantB);
        });
    }

    /**
     * @param \App\Models\Page\Page $variantA
     * @param \App\Models\Page\Page $variantB
     */
    public function activate(Page $variantA, Page $variantB): void
    {
        DB::transaction(static function () use ($variantA, $variantB) {
            $variantA->testing_a_id = null;
            $variantA->testing_b_id = $variantB->getKey();
            $variantA->save();

            $variantB->testing_a_id = $variantA->getKey();
            $variantB->testing_b_id = null;

            Page::withoutEvents(static function () use ($variantB) {
                $variantB->save();
            });
        });
    }

    /**
     * @param \App\Models\Page\Page $variantB
     * @param mixed[] $data
     */
    public function updateTestingCounterpart(Page $variantB, array $data): void
    {
        $changeableAttributes = [
            'name', 'view', 'image_id',
            'seo_title', 'seo_description', 'seo_index', 'seo_follow', 'seo_sitemap',
            'open_graph'
        ];

        $variantB->fill(Arr::only($data, $changeableAttributes));

        Page::withoutEvents(static function () use ($variantB) {
            Page::withoutScope(ABTestingScope::class, static function () use ($variantB) {
                $variantB->save();
            });
        });
    }

    /**
     * @param \App\Models\Page\Page $variantA
     * @param mixed[] $data
     */
    public function updateTestedPage(Page $variantA, array $data): void
    {
        DB::transaction(static function () use ($data, $variantA) {
            $variantA->fill($data)->save();

            $attributesToSync = [
                'published', 'publish_at', 'unpublish_at'
            ];

            if ($variantA->wasChanged($attributesToSync)) {
                $variantB = $variantA->getTestingVariantB();
                $variantB->fill(Arr::only($data, $attributesToSync));

                Page::withoutScope(ABTestingScope::class, static function () use ($variantB) {
                    $variantB->save();
                });
            }
        });
    }

    /**
     * Stop AB testing while keeping only variant A.
     *
     * @param \App\Models\Page\Page $variantA
     */
    public function stopAndKeepA(Page $variantA): void
    {
        $variantB = $variantA->getTestingVariantB();

        DB::transaction(function () use ($variantA, $variantB) {
            $this->deactivateTestingOnPage($variantA);
            $this->deleteVariantB($variantB->getKey());
        });
    }

    /**
     * Stop AB testing while keeping only variant B.
     *
     * @param \App\Models\Page\Page $variantA
     */
    public function stopAndKeepB(Page $variantA): void
    {
        $variantB = $variantA->getTestingVariantB();

        DB::transaction(function () use ($variantA, $variantB) {
            $this->deactivateTestingOnPage($variantB);
            $variantA->delete();

            $this->copyAttributesToB($variantA, $variantB);
            $variantB->is_homepage = $variantA->is_homepage;
            $variantB->url = $variantA->url;

            Page::withoutScope(ABTestingScope::class, static function () use ($variantB) {
                $variantB->save();
            });
        });
    }

    /**
     * Stop AB testing while keeping both variants.
     *
     * @param \App\Models\Page\Page $variantA
     */
    public function stopAndKeepBoth(Page $variantA): void
    {
        $variantB = $variantA->getTestingVariantB();

        DB::transaction(function () use ($variantA, $variantB) {
            $this->deactivateTestingOnPage($variantA);
            $this->deactivateTestingOnPage($variantB);
            $this->copyAttributesToB($variantA, $variantB);

            Page::withoutScope(ABTestingScope::class, static function () use ($variantB) {
                $variantB->save();
            });
        });
    }


    /**
     * @param int $variantBId
     */
    public function deleteVariantB(int $variantBId): void
    {
        Page::withTestingCounterparts()->where('id', $variantBId)->delete();
    }


    /**
     * @param \App\Models\Page\Page $page
     */
    private function deactivateTestingOnPage(Page $page): void
    {
        $page->testing_a_id = null;
        $page->testing_b_id = null;
        Page::withoutScope(ABTestingScope::class, static function () use ($page) {
            $page->save();
        });
    }

    /**
     * @param \App\Models\Page\Page $variantA
     * @param \App\Models\Page\Page $variantB
     */
    private function copyAttributesToB(Page $variantA, Page $variantB): void
    {
        $variantB->fill(
            $variantA->only([
                'seo_index', 'seo_follow', 'seo_sitemap',
                'published', 'publish_at', 'unpublish_at'
            ])
        );
    }
}
