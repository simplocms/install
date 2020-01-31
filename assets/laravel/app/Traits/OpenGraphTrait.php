<?php

namespace App\Traits;

use App\Structures\DataTypes\OpenGraphSettings;

/**
 * Trait OpenGraphTrait
 * @package App\Traits
 * @author Patrik Václavek
 * @copyright SIMPLO, s.r.o.
 * @mixin \Illuminate\Database\Eloquent\Model
 *
 * @property \App\Structures\DataTypes\OpenGraphSettings open_graph
 */
trait OpenGraphTrait
{
    /** @var \App\Structures\DataTypes\OpenGraphSettings */
    private $openGraphSettings;

    /**
     * Set open graph settings attribute.
     *
     * @param array|null $value
     */
    public function setOpenGraphAttribute(?array $value)
    {
        if (is_null($value) || !array_filter($value)) {
            $this->attributes['open_graph'] = null;
        } else {
            $this->openGraphSettings = OpenGraphSettings::make($value);
            $this->attributes['open_graph'] = $this->openGraphSettings->toDatabaseJson();
        }
    }


    /**
     * Get open graph settings attribute.
     *
     * @return \App\Structures\DataTypes\OpenGraphSettings
     */
    public function getOpenGraphAttribute(): OpenGraphSettings
    {
        if ($this->openGraphSettings) {
            return $this->openGraphSettings;
        }

        $raw = $this->attributes['open_graph'] ?? null;
        if (!$raw) {
            return $this->openGraphSettings = new OpenGraphSettings();
        }

        return $this->openGraphSettings = OpenGraphSettings::make(json_decode($raw, true));
    }
}
