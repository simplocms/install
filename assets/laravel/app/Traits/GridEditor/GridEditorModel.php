<?php

namespace App\Traits\GridEditor;

use App\Models\Interfaces\IsGridEditorContent;
use App\Models\Module\Module;
use App\Models\Web\Language;
use App\Structures\GridEditor\ContentTree;
use Illuminate\Support\Collection;

/**
 * Trait GridEditorModel
 * @package App\Traits\GridEditor
 * @author Patrik Václavek
 * @mixin \Illuminate\Database\Eloquent\Model
 * @copyright SIMPLO, s.r.o.
 */
trait GridEditorModel
{
    /**
     * Active content.
     *
     * @var \App\Models\Interfaces\IsGridEditorContent
     */
    private $cachedContent;

    /**
     * Contents (content versions) of grid editor.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    abstract public function contents(): \Illuminate\Database\Eloquent\Relations\HasMany;


    /**
     * Get content for specified language.
     *
     * @param \App\Models\Web\Language|int $language
     *
     * @return \App\Models\Interfaces\IsGridEditorContent|null
     */
    public function getLanguageContent($language)
    {
        if ($this->cachedContent) {
            return $this->cachedContent;
        }

        return $this->cachedContent = $this->contents()->whereLanguage($language)->first();
    }


    /**
     * Create new version of content and deactivate old one.
     *
     * @param string $content
     * @param array $attributes
     *
     * @return \App\Models\Interfaces\IsGridEditorContent
     */
    public function createNewContent(string $content = null, array $attributes = []): IsGridEditorContent
    {
        $activeContentId = null;
        $contentData = ['content' => $content];

        if ($this->usesGridEditorVersions()) {
            $activeContent = $this->wasRecentlyCreated ? null : $this->getActiveContent();
            $activeContentId = $activeContent ? $activeContent->getKey() : null;
            $contentData['is_active'] = true;

            if (!isset($attributes['author_user_id'])) {
                $attributes['author_user_id'] = auth()->id();
            }
        }

        $contentsClass = $this->contents()->getRelated();
        /** @var \App\Models\Interfaces\IsGridEditorContent $newContent */
        $newContent = new $contentsClass($contentData);
        $newContent->forceFill($attributes);
        $newContent = $this->contents()->save($newContent);

        if ($activeContentId) {
            $this->contents()->where('id', $activeContentId)
                ->update(['is_active' => false]);
        }

        return $this->cachedContent = $newContent;
    }


    /**
     * Active version of content.
     * @cached
     *
     * @return \App\Models\Interfaces\IsGridEditorContent|null
     */
    public function getActiveContent()
    {
        if (!$this->cachedContent) {
            $this->cachedContent = $this->contents()->where('is_active', 1)->first();
        }

        return $this->cachedContent;
    }


    /**
     * Set active version.
     *
     * @param int $contentId
     */
    public function setActiveContent(int $contentId): void
    {
        $content = $this->contents()->find($contentId);

        if (!$content) {
            return;
        }

        $this->contents()
            ->where('id', '<>', $contentId)
            ->update([
                'is_active' => false
            ]);

        $content->update([
            'is_active' => true
        ]);

        $this->cachedContent = $content;
    }


    /**
     * @param array|null $content
     * @return \App\Models\Interfaces\IsGridEditorContent|null
     * @throws \App\Exceptions\GridEditorException
     */
    public function createNewVersionIfChanged(array $content = null): ?IsGridEditorContent
    {
        $activeContent = $this->wasRecentlyCreated ? null : $this->getActiveContent();

        if (!$content) {
            if ($this->wasRecentlyCreated && !$activeContent) {
                return $this->createNewContent();
            }

            return $activeContent ? $this->createNewContent() : null;
        }

        $contentTree = ContentTree::parse($content);
        /** @var \App\Structures\GridEditor\ContentTree $activeContentTree */
        $activeContentTree = optional($activeContent)->getContentTree();

        // Contents are equal
        if ($activeContentTree && $activeContentTree->isEqual($contentTree)) {
            return null;
        }

        $createdModules = []; // newly added modules
        $updatedModules = []; // changed modules
        $originalModules = []; // unchanged modules (if new version is created, these will be duplicated)

        $contentTree->prefetchAllEntities();

        // #1 MAP CHANGES
        foreach ($contentTree->getModules() as $index => $module) {
            if ($module->shouldExist()) {
                $entity = $module->getEntity();

                // When entity does not exist, continue.
                if (!$entity) {
                    continue;
                }

                // Is creating new model (so entity is stolen) OR entity belongs to different content (stolen).
                if (!$activeContent || !$entity->doesBelongToContent($activeContent)) {
                    $updatedModules[$index] = true;
                } else {
                    // Is updating existing model and entity belongs to the content of this model.
                    // If model has configuration, it means it was changed in GridEditor
                    if ($module->hasConfiguration()) {
                        // Check if entity configuration needs to be updated.
                        if ($entity->hasChanged($module->getConfiguration())) {
                            $updatedModules[$index] = true;
                        }
                    } else {
                        $originalModules[$index] = true;
                    }
                }
            } elseif ($module->moduleExists()) {
                // CREATING NEW MODULES
                // If module with specified name does not exist, continue.
                $createdModules[$index] = true;
            }
        }

        // Check if new version needs to be created.
        if (!$createdModules && !$updatedModules && $activeContent &&
            $activeContentTree && $activeContentTree->isEqual($contentTree)
        ) {
            return null;
        }

        // Update variables - new version and content.
        $newContent = $this->createNewContent();

        // Save entities and configurations.
        foreach ($contentTree->getModules() as $index => $module) {
            // Create new entities.
            if (isset($createdModules[$index])) {
                $module->createEntityForContent($newContent);
            } else if (isset($updatedModules[$index]) || isset($originalEntities[$index])) {
                $module->duplicateToContent($newContent);
            }
        }

        $newContent->setContentAttribute($contentTree->toJson());
        $newContent->save();
        return $newContent;
    }


    /**
     * Get versions as JSON.
     *
     * @return array
     */
    public function getGridEditorVersions(): array
    {
        $versions = $this->contents()
            ->with(['author'])
            ->orderBy('created_at', 'DESC')
            ->get();

        $versionsCount = $versions->count();
        $output = [];

        foreach ($versions as $index => $version) {
            $output[] = [
                'id' => $version->id,
                'isActive' => (bool)$version->is_active,
                'date' => $version->created_at->format('j.n.Y H:i'),
                'author' => $version->author->name ?? null,
                'index' => $versionsCount - $index
            ];
        }

        return $output;
    }


    /**
     * Does the model versions its content with grid editor?
     *
     * @return boolean
     */
    public function usesGridEditorVersions(): bool
    {
        return $this->useGridEditorVersions ?? false;
    }


    /**
     * Does the model localize its content with grid editor?
     *
     * @return boolean
     */
    public function usesLocalizedContent(): bool
    {
        return $this->useLocalizedContent ?? false;
    }


    /**
     * Search grid content.
     *
     * @param string $term
     * @param \Illuminate\Support\Collection $models
     * @param \App\Models\Web\Language $language
     * @param callable|null $evaluator
     * @return \Illuminate\Support\Collection
     */
    protected static function searchGridContent(
        string $term, Collection $models, Language $language, callable $evaluator = null
    ): Collection
    {
        $results = [];
        Module::$searchRendering = true;

        /** @var \App\Traits\GridEditor\GridEditorModel $model */
        foreach ($models as $model) {
            if ($evaluator && $evaluator($model)) {
                $results[] = $model;
                continue;
            }

            $model->cachedContent = $model->contents->first();
            if (!$model->cachedContent) {
                continue;
            }

            $content = $model->cachedContent->getHtml(compact('language'));
            $rawText = strip_tags($content);

            if (mb_stripos($rawText, $term) !== false) {
                $results[] = $model;
            }
        }
        Module::$searchRendering = false;

        return collect($results);
    }
}
