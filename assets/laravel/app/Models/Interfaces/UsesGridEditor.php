<?php

namespace App\Models\Interfaces;

interface UsesGridEditor
{
    /**
     * Contents (versions).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contents(): \Illuminate\Database\Eloquent\Relations\HasMany;


    /**
     * Get content for specified language.
     *
     * @param \App\Models\Web\Language|int $language
     * 
     * @return \App\Models\Interfaces\IsGridEditorContent
     */
    public function getLanguageContent($language);


    /**
     * Create new version of content and deactivate old one.
     *
     * @param string $content
     * @param array  $attributes
     * 
     * @return \App\Models\Interfaces\IsGridEditorContent
     */
    public function createNewContent(string $content, array $attributes = []): \App\Models\Interfaces\IsGridEditorContent;


    /**
     * Does the model versions its content with grid editor?
     *
     * @return boolean
     */
    public function usesGridEditorVersions(): bool;
}
