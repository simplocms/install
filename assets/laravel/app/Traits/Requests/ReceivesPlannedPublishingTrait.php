<?php

namespace App\Traits\Requests;

use App\Helpers\Functions;

/**
 * Trait ReceivesPlannedPublishingTrait
 * @package App\Traits\Requests
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 * @mixin \Illuminate\Http\Request
 */
trait ReceivesPlannedPublishingTrait
{
    /**
     * Get publish at date.
     *
     * @return \DateTime
     */
    public function getPublishAt(): \DateTime
    {
        $date = Functions::createDateFromFormat(
            'd.m.Y H:i',
            ($this->input('publish_at_date') ?? date('d.m.Y'))
            . " " . ($this->input('publish_at_time') ?? date('H:i'))
        );

        return $date ?? new \DateTime();
    }


    /**
     * Get unpublish at date.
     *
     * @return \DateTime|null
     */
    public function getUnpublishAt(): ?\DateTime
    {
        return Functions::createDateFromFormat(
            'd.m.Y H:i',
            $this->input('unpublish_at_date')
            . " " . $this->input('unpublish_at_time')
        );
    }
}
