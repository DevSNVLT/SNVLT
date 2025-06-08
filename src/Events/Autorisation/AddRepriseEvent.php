<?php

namespace App\Events\Autorisation;

use App\Entity\Autorisation\Reprise;

class AddRepriseEvent
{
    const ADD_REPRISE_EVENT = 'reprise.edit';

    public function __construct(private Reprise $reprise){}

    public function getReprise() : Reprise
    {
        return $this->reprise;
    }
}