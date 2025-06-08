<?php

namespace App\Controller\References;

use App\Repository\References\EssenceRepository;
use App\Repository\References\ZoneHemispheriqueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ZoneHemispheriqueController extends AbstractController
{
    #[Route('/snvlt/zoneh/lstjson', name: 'zoneh.json')]
    public function zonehJson(ZoneHemispheriqueRepository $zonesRepo): Response
    {
        $zones = array();
        $liste_zones = $zonesRepo->findAll();

        foreach ($liste_zones as $zone){
            $zones[] = array(
                'zone_id'=>$zone->getId(),
                'zone_nom'=>$zone->getZone()
            );
        }

        return new JsonResponse(json_encode($zones));
    }
}
