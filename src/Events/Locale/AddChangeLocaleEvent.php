<?php

namespace App\Events\Locale;

use App\Entity\Administration\StockDoc;
use App\Entity\Autorisation\Attribution;

class AddChangeLocaleEvent
{
    const ADD_LOCALE_EVENT = 'changeLocale';

    public function __construct(private string $lang){}

    public function getLocale() : string
    {
        return $this->lang;
    }
}