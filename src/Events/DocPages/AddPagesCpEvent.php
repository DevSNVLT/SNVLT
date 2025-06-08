<?php

namespace App\Events\DocPages;

use App\Entity\Autorisation\Reprise;
use App\Entity\DocStats\Entetes\Documentcp;

class AddPagesCpEvent
{
    const ADD_PAGECP_EVENT = 'app_demande_gen_doccp';

    public function __construct(private Documentcp $doc_cp){}

    public function getDocumentCp() : Documentcp
    {
        return $this->doc_cp;
    }
}