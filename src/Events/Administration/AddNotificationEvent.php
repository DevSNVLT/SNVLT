<?php

namespace App\Events\Administration;

use App\Entity\User;

class AddNotificationEvent
{
    const ADD_NOTIFICATION_EVENT = 'secure_au_user';

    public function __construct(private User $user){}

    public function getUser() : User
    {
        return $this->user;
    }
}