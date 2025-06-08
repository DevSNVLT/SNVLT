<?php

namespace App\EventListeners;


use App\Controller\Services\ImportProspectionService;
use App\Entity\Autorisation\Attribution;
use App\Entity\References\Foret;
use App\Events\Administration\AddFicheProspectionEvent;
use App\Events\Autorisation\AddAttributionEvent;
use App\Events\Autorisation\AddRepriseEvent;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

class InventaireListener
{
    public function __construct( private ImportProspectionService $prospectionService,
        private LoggerInterface $logger)
    {
    }

    public function onFicheProspectionAdd(AddFicheProspectionEvent $event){
        $fichier = $event->getFicheProspection()->getLienComplet();

        $this->replaceComma($fichier);

        $this->prospectionService->importCsvProspectionTemp($fichier, $event->getFicheProspection());

        $this->logger->info("Fichier Temporaire enregistré");
    }

    public function replaceComma ($chemin_fichier){
        //Ouvre le fichier en écriture
            $fichier = fopen($chemin_fichier, 'r+');

        //Remplacer les points-virgules(;) en (,)
        $contenu = file_get_contents($chemin_fichier);
        $contenu_modifie = str_replace(';', ',', $contenu);


        //Ecriture et enregistrement des modifications
        fwrite($fichier, $contenu_modifie);
        fclose($fichier);
    }
}