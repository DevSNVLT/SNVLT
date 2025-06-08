<?php

namespace App\Controller\Services\synchro;

use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

class ConnectionAlt
{
    public function __construct(private ManagerRegistry $registry)
    {
    }

    public function connect($sql){
        $connection = $this->registry->getConnection("alt");
        $result = $connection
            ->prepare($sql)
            ->executeQuery()
            ->fetchAllAssociative();
    }
}