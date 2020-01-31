<?php

namespace App\Http\Requests\Admin;

use App\Contracts\PhotogalleryInterface;
use App\Http\Requests\AbstractFormRequest;
use App\Models\Article\Article;
use App\Models\Article\Category;
use App\Models\User;
use App\Rules\MediaFileRule;
use App\Rules\MediaImageRule;
use App\Traits\Requests\ReceivesPlannedPublishingTrait;
use App\Traits\Requests\ValidatesAndReceivesPhotogalleryTrait;
use App\Traits\Requests\ValidatesOpenGraphTrait;
use App\Traits\Requests\ValidatesSeoTrait;
use Illuminate\Validation\Rule;

class ArticleRequest extends AbstractFormRequest
{
    use ValidatesAndReceivesPhotogalleryTrait,
        ReceivesPlannedPublishingTrait,
        ValidatesSeoTrait,
        ValidatesOpenGraphTrait;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'title' => 'required|max:250',
            'type' => 'numeric|min:1|max:3',
            'url' => 'required|max:250',
            'perex' => 'required',
            'image_id' => ['nullable', new MediaImageRule],
            'video_id' => ['nullable', new MediaFileRule(Article::getAllowedVideoMimeTypes())],

            // Categories validation
            'categories' => 'array',
            'categories.*' => [
                Rule::exists(Category::getTableName(), 'id')
            ],
        ];

        if (auth()->user()->isAdmin()) {
            $rules['user_id'] = ['required', Rule::exists(User::getTableName(), 'id')];
        }

        return $this->mergeRules(
            $rules, $this->getPhotogalleryRules(), $this->getSeoRules(), $this->getOpenGraphRules()
        );
    }


    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return $this->mergeMessages(
            'admin/article/form.messages', $this->getSeoMessages(), $this->getOpenGraphMessages()
        );
    }


    /**
     * Return input values.
     *
     * @return array
     */
    public function getValues(): array
    {
        $input = $this->all([
            'title', 'perex', 'text', 'type', 'state', 'url', 'image_id', 'open_graph',
            'seo_title', 'seo_description', 'seo_index', 'seo_follow', 'seo_sitemap',
            'video_id'
        ]);

        $input['publish_at'] = $this->getPublishAt();
        $input['unpublish_at'] = $this->get('set_unpublish_at') ? $this->getUnpublishAt() : null;

        return $input;
    }


    /**
     * Return categories ids.
     *
     * @return int[]
     */
    public function getCategories(): array
    {
        $categories = $this->input('categories');
        return is_array($categories) ? $categories : [];
    }


    /**
     * Return tags
     *
     * @return string[]
     */
    public function getTags(): array
    {
        $tags = $this->input('tags', '');

        if (!strlen($tags)) {
            return [];
        }

        return array_unique(array_map('trim', explode(',', $tags)));
    }


    /**
     * Get content.
     *
     * @return array|null
     */
    public function getGridEditorContent(): ?array
    {
        return $this->input('content');
    }


    /**
     * Get instance of model using photogallery.
     *
     * @return \App\Contracts\PhotogalleryInterface
     */
    protected function getModelWithPhotogallery(): PhotogalleryInterface
    {
        /** @var \App\Models\Article\Article $article */
        $article = $this->route('article');
        return $article ?? new Article;
    }
}
