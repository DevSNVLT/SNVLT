<?php

namespace App\EventListeners;


use App\Controller\Services\synchro\ForetSnvlt1;
use App\Controller\Services\synchro\Mouvements\AttributionSnvlt1;
use App\Entity\Autorisation\Attribution;
use App\Entity\References\Foret;
use App\Events\Autorisation\AddAttributionEvent;
use App\Events\Autorisation\AddRepriseEvent;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

class AutorisationsListener
{
    public function __construct(private ManagerRegistry $doctrine, private LoggerInterface $logger, private AttributionSnvlt1 $attributionSnvlt1, private ForetSnvlt1 $foretSnvlt1)
    {
    }

    public function onAttributionAdd(AddAttributionEvent $event){

        $foret = new  Foret();

        $repository= $this->doctrine->getRepository(Foret::class);
        $foret  =$repository->find($event->getAttribution()->getCodeForet());
        $foret ->setAttribue(true);

        $manager = $this->doctrine->getManager();
        $manager->persist($foret);
        $manager->flush();

    }


    public function onRepriseAdd(AddRepriseEvent $event){

        $attribution = new  Attribution();
        $foret = new Foret();

        //Mise à jour de l'attribution
        $repository= $this->doctrine->getRepository(Attribution::class);
        $attribution  =$repository->find($event->getReprise()->getCodeAttribution());
        $attribution ->setReprise(true);
        $manager = $this->doctrine->getManager();
        $manager->persist($attribution);



        //Mise à jour de la forêt
        $repository_foret= $this->doctrine->getRepository(Foret::class);
        $foret  =$repository_foret->find($attribution->getCodeForet());
        $foret ->setReprise(true);
        $foret ->setAttribue(true);
        $manager_foret = $this->doctrine->getManager();
        $manager_foret->persist($foret);

        //Enregistrement en base de toutes les transactions
        $manager->flush();

        // Mise à jour dans SNVLT1
        $this->attributionSnvlt1->MajRepriseAtt($attribution, true);
        $this->foretSnvlt1->MajRepriseAttributionForet($foret);


        //Message dans le Log
        $this->logger->info("Attribution.reprise, Foret.reprise and Foret.attribue updated succesfully from Reprise Added");
    }
}