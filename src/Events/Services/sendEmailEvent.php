<?php

namespace App\Events\Services;

use App\Entity\Autorisation\Reprise;
use App\Entity\User;

class sendEmailEvent
{
    const ADD_EMAIL_EVENT = 'add_member_request';
    public function __construct(private User $responsable){}
    public function getResponsable() : User{
        return $this->responsable;
    }
}