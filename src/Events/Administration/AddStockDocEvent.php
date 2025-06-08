<?php

namespace App\Events\Administration;

use App\Entity\Administration\StockDoc;

class AddStockDocEvent
{
    const ADD_DOCSTOCK_EVENT = 'stockdoc.edit';

    public function __construct(private StockDoc $stockDoc){}

    public function getStockdoc() : StockDoc
    {
        return $this->stockDoc;
    }
}