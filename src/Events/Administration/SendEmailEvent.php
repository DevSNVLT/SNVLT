<?php

namespace App\Events\Administration;

use App\Entity\User;

class SendEmailEvent
{
    const SEND_EMAIL_EVENT = 'secure_au_user';

    public function __construct(private User $user){}

    public function getUser() : User
    {
        return $this->user;
    }
}