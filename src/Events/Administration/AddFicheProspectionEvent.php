<?php

namespace App\Events\Administration;

use App\Entity\Administration\FicheProspection;
use App\Entity\Autorisation\Attribution;

class AddFicheProspectionEvent
{
    const ADD_FICHE_PROSPECTION_EVENT = 'app_inventaire';

    public function __construct(private FicheProspection $ficheProspection){}

    public function getFicheProspection() : FicheProspection
    {
        return $this->ficheProspection;
    }
}