<?php

namespace App\Observers;

use App\Models\Interfaces\UrlInterface;

class UrlObserver
{
    /**
     * Friendlify url attribute when creating new model.
     *
     * @param UrlInterface $model
     * @return void
     */
    public function creating(UrlInterface $model)
    {
        $model->friendlifyUrlAttribute();
    }


    /**
     * Friendlify url attribute when updating model.
     *
     * @param UrlInterface $model
     * @return void
     */
    public function updating(UrlInterface $model)
    {
        if ($model->shouldUpdateUrls()) {
            $model->friendlifyUrlAttribute();
        }
    }


    /**
     * Listen to the model created event.
     *
     * @param  UrlInterface  $model
     * @return void
     */
    public function created(UrlInterface $model)
    {
        if (!$model->syncUrlsManually()) {
            $model->createUrls();
        }
    }


    /**
     * Listen to the model updated event.
     *
     * @param  UrlInterface  $model
     * @return void
     */
    public function updated(UrlInterface $model)
    {
        if (!$model->syncUrlsManually() && $model->shouldUpdateUrls()) {
            $model->updateUrls();
        }
    }


    /**
     * Listen to the model deleting event.
     *
     * @param  UrlInterface  $model
     * @return void
     */
    public function deleted(UrlInterface $model)
    {
        $model->deleteUrls();
    }


    /**
     * Listen to the model restore event.
     *
     * @param  UrlInterface  $model
     * @return void
     */
    public function restored(UrlInterface $model)
    {
        $model->restoreUrls();
    }
}
