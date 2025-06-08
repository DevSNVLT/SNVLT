<?php

namespace App\Events\Autorisation;

use App\Entity\Autorisation\Attribution;

class AddAttributionEvent
{
    const ADD_ATTRIBUTION_EVENT = 'attribution.edit';

    public function __construct(private Attribution $attribution){}

    public function getAttribution() : Attribution
    {
        return $this->attribution;
    }
}