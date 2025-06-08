<?php

namespace App\Controller;
use Doctrine\DBAL\Connection;
use App\Entity\Pef;
use App\Repository\PefRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class SpacialDataLayerController extends AbstractController
{

    #[Route('/spatial/layers', name: 'app_layers_pef')]
    public function carto_index(Connection $connection):Response
    {   
        $sql = "SELECT numero_pef FROM pef";
        $stmt = $connection->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $results = $resultSet->fetchAllAssociative();

        return $this->render('spacial_data_layer/index.html.twig', [
            'data' => $results,
        ]);
    }


}
