<?php

namespace App\Controller\Services;

use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

class ProjectionQuery
{
    private $registry;
    private $connection;
    private $request;
    public function __construct(ManagerRegistry $registry, RequestStack $request,Connection $connection)
    {
        $this->registry = $registry;
        $this->request = $request;
        $this->connection = $connection;
    }

    public function getBilleProjectionCoordinate(int $idChargement){
        $sql = "SELECT ST_AsGeoJSON(ST_Transform(ST_SetSRID(ST_MakePoint(metier.lignepagebrh.x_lignepagebrh, metier.lignepagebrh.y_lignepagebrh), 32630), 4326 )) AS geom 
                        FROM metier.lignepagebrh WHERE code_pagebrh_id =:idChargement";
        $stmt = $this->connection->prepare($sql);
        $result = $stmt->executeQuery(['idChargement' => $idChargement]);
        return $result->fetchAssociative();
    }


}