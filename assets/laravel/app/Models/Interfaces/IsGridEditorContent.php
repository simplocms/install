<?php

namespace App\Models\Interfaces;

use App\Models\Module\Entity;
use App\Structures\GridEditor\ContentTree;

/**
 * Interface IsGridEditorContent
 * @package App\Models\Interfaces
 * @mixin \Illuminate\Database\Eloquent\Model
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 */
interface IsGridEditorContent
{
    /**
     * Entities in content.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function entities(): \Illuminate\Database\Eloquent\Relations\MorphMany;


    /**
     * Set content attribute. Replace whitespaces between <div> tags.
     *
     * @param string $value
     */
    public function setContentAttribute(string $value);


    /**
     * Get HTML content.
     *
     * @param array $renderAttributes
     * 
     * @return string
     */
    public function getHtml(array $renderAttributes = []): string;


    /**
     * Get raw content.
     *
     * @return string
     */
    public function getRaw(): string;


    /**
     * Get content as associative array.
     *
     * @return \App\Structures\GridEditor\ContentTree|null
     */
    public function getContentTree(): ?ContentTree;


    /**
     * Update content.
     *
     * @param array|null $content
     * @throws \App\Exceptions\GridEditorException
     */
    public function updateContentAndModules(array $content = null): void;


    /**
     * Create module entity for the content.
     *
     * @param string $moduleName
     * @param array $configuration
     * @return \App\Models\Module\Entity
     */
    public function createModuleEntity(string $moduleName, array $configuration): Entity;
}
