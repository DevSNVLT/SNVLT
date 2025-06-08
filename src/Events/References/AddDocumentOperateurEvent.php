<?php

namespace App\Events\References;

use App\Entity\References\DocumentOperateur;

class AddDocumentOperateurEvent
{
    const ADD_DOCUMENT_OPERATEUR_EVENT = 'document_operateur.edit';

    public function __construct(private DocumentOperateur $documentOperateur){}

    public function getDocumentOperateur() : DocumentOperateur
    {
        return $this->documentOperateur;
    }
}