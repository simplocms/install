<?php

namespace App\Traits;

use Carbon\Carbon;

/**
 * Trait HasPlannedPublishing
 * @package App\Traits
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 * @mixin \Illuminate\Database\Eloquent\Model
 *
 * @property \Carbon\Carbon|null publish_at
 * @property \Carbon\Carbon|null unpublish_at
 *
 * @property-read string|null publish_at_date
 * @property-read string|null publish_at_time
 * @property-read string|null unpublish_at_date
 * @property-read string|null unpublish_at_time
 *
 * @method static \Illuminate\Database\Eloquent\Builder publishedByDate()
 * @method static \Illuminate\Database\Eloquent\Builder orderPublish()
 */
trait PlannedPublishingTrait
{
    /**
     * Select only published articles
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopePublishedByDate($query)
    {
        $query->where('publish_at', '<=', Carbon::now())
            ->where(function ($query) {
                /** @var \Illuminate\Database\Eloquent\Builder $query */
                $query->where('unpublish_at', '>=', Carbon::now())
                    ->orWhere('unpublish_at', null);
            });
    }


    /**
     * Order articles by publish date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $dir
     */
    public function scopeOrderPublish($query, string $dir = 'desc')
    {
        $query->orderBy("{$this->table}.publish_at", $dir === 'desc' ? 'desc' : 'asc');
    }


    /**
     * Check if article is public.
     *
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this->isInPublicPeriod();
    }


    /**
     * Check if model is withing its published period.
     *
     * @return bool
     */
    public function isInPublicPeriod(): bool
    {
        return (!$this->publish_at || $this->publish_at->isPast()) &&
            (!$this->unpublish_at || $this->unpublish_at->isFuture());
    }


    /**
     * Get formatted date of publish_at timestamp.
     *
     * @return string|null
     */
    public function getPublishAtDateAttribute(): ?string
    {
        return optional($this->publish_at)->format('d.m.Y');
    }


    /**
     * Get formatted date of unpublish_at timestamp.
     *
     * @return string|null
     */
    public function getUnpublishAtDateAttribute(): ?string
    {
        return optional($this->unpublish_at)->format('d.m.Y');
    }


    /**
     * Get formatted date of publish_at timestamp.
     *
     * @return string|null
     */
    public function getPublishAtTimeAttribute(): ?string
    {
        return optional($this->publish_at)->format('H:i');
    }


    /**
     * Get formatted date of publish_at timestamp.
     *
     * @return string|null
     */
    public function getUnpublishAtTimeAttribute(): ?string
    {
        return optional($this->unpublish_at)->format('H:i');
    }
}
