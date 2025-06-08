<?php

namespace App\Events\References;

use App\Entity\Administration\DemandeOperateur;

class AddDemandeOperateurEvent
{
    const ADD_DEMANDE_OPERATEUR_EVENT = 'reprises_op.add';

    public function __construct(private DemandeOperateur $demandeOperateur){}

    public function getDemandeOperateur() : DemandeOperateur
    {
        return $this->demandeOperateur;
    }
}