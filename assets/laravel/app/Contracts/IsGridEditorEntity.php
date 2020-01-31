<?php

namespace App\Contracts;

use App\Models\Interfaces\IsGridEditorContent;

/**
 * Interface IsGridEditorEntity
 * @package App\Contracts
 * @mixin \Illuminate\Database\Eloquent\Model
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 */
interface IsGridEditorEntity
{
    /**
     * Has configuration changed - hence entity was changed.
     *
     * @param array $configuration
     * @return bool
     */
    public function hasChanged(array $configuration): bool;

    /**
     * Does entity belong to specified content?
     *
     * @param \App\Models\Interfaces\IsGridEditorContent $content
     * @return bool
     */
    public function doesBelongToContent(IsGridEditorContent $content): bool;

    /**
     * Update configuration of the entity.
     *
     * @param array $configuration
     */
    public function updateConfiguration(array $configuration): void;

    /**
     * Duplicate entity for new content.
     *
     * @param \App\Models\Interfaces\IsGridEditorContent $content
     * @param array|null $configuration
     * @return \App\Contracts\IsGridEditorEntity
     */
    public function duplicateForNewContent(IsGridEditorContent $content, array $configuration  = null): IsGridEditorEntity;

    /**
     * Render entity.
     *
     * @param array $renderAttributes
     * @return string
     */
    public function render(array $renderAttributes = []): string;
}
