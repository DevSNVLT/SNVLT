<?php

namespace App\EventListeners;

use App\Controller\Services\Utils;
use App\Entity\Admin\Option;
use App\Entity\References\CircuitCommunication;
use App\Entity\References\DetailsModele;
use App\Entity\References\Direction;
use App\Entity\References\Exploitant;
use App\Entity\References\Exportateur;
use App\Entity\References\ModeleCommunication;
use App\Entity\References\ServiceMinef;
use App\Entity\References\Usine;
use App\Entity\User;
use App\Events\References\AddDemandeOperateurEvent;
use App\Events\References\AddDocumentOperateurEvent;
use App\Repository\References\CircuitCommunicationRepository;
use App\Repository\References\PageDocGenRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProcessCircuitGenerate
{
    private  $pageDocGenRepository;
    private $translator;
    public function __construct(
        private ManagerRegistry $doctrine,
        private LoggerInterface $logger,
        PageDocGenRepository $pageDocGenRepository,
        TranslatorInterface $translator,
        private Utils $utils,
        private ManagerRegistry $registry,

        private CircuitCommunicationRepository $communicationRepository,
    )
    {
        $this->pageDocGenRepository = $pageDocGenRepository;
        $this->translator = $translator;
    }

    public function onDocumentOperateurAdd(
        AddDocumentOperateurEvent $event
    ){
        $modele = new  ModeleCommunication();
        $manager = $this->doctrine->getManager();

        $option_legalite = $this->registry->getRepository(Option::class)->findOneBy(['name'=>'legalite_document']);
        $modeles = $this->doctrine->getRepository(ModeleCommunication::class)->findBy(['statut'=>'ACTIF', 'code_type_modele_communication'=>2]);
        $modele = $modeles[0] ;

        //Recherche le circuit du modèle actif
        $details_modele = $this->doctrine->getRepository(DetailsModele::class)->findBy(['code_modele'=>$modele]);


        // ------------ Parcours du circuit modèle et Enregistrepment Copie du circuit de la demande -------------------------
        $i = 0;
        $dateCreation =  new \DateTimeImmutable();
        foreach ($details_modele as $detail){
            $i++;
            $circuit = new  CircuitCommunication();

            $circuit->setCodeModele($modele);
            $circuit->setCodeDirection($detail->getCodeDirection());
            $circuit->setTypeService($detail->getTypeService());
            $circuit->setCodeService($detail->getCodeService());
            $circuit->setCodeDocumentOperateur($event->getDocumentOperateur());
            $circuit->setNumSeq($detail->getNumseq());
            $circuit->setCreatedBy($event->getDocumentOperateur()->getCreatedBy());
            $circuit->setCreatedAt($dateCreation);

            $operateur = "";

            if ($event->getDocumentOperateur()->getTypeOperateur()->getId() == 2){
                $operateur = $this->registry->getRepository(Exploitant::class)->find($event->getDocumentOperateur()->getCodeOperateur())->getRaisonSocialeExploitant();
            }elseif ($event->getDocumentOperateur()->getTypeOperateur()->getId() == 3){
                $operateur = $this->registry->getRepository(Usine::class)->find($event->getDocumentOperateur()->getCodeOperateur())->getRaisonSocialeUsine();
            }elseif ($event->getDocumentOperateur()->getTypeOperateur()->getId() == 4){
                $operateur = $this->registry->getRepository(Exportateur::class)->find($event->getDocumentOperateur()->getCodeOperateur())->getRaisonSocialeExportateur();
            }

            $circuit->setOperateur($operateur);
            $userReference = new User();
            if ($option_legalite->getValue() == 0){
                $circuit->setStatut("VALIDE");
            } else if($i == 1){
                $circuit->setStatut("EN COURS");

            }



            $manager->persist($circuit);
            $manager->flush();
        }

        if ($option_legalite->getValue() == 0){
            $event->getDocumentOperateur()->setStatut("APPROUVE");
            $manager->persist($event->getDocumentOperateur());
            $manager->flush();
        } else {
            /*Recherche de l'ID du premier circuit a partir du code document*/
            $idCircuit = $this->communicationRepository->findBy(['code_document_operateur'=>$event->getDocumentOperateur()])[0];

            /*Début Notification*/
            if ($idCircuit->getTypeService() == 'SERVICE'){
                //Recherche du mail Responsable Service
                $serviceReference = $this->registry->getRepository(ServiceMinef::class)->find($idCircuit->getCodeService());
                $userReference = $this->registry->getRepository(User::class)->findBy(['email'=>$serviceReference->getEmailPersonneRessource()]);

                $this->utils->envoiNotification(
                    $this->registry,
                    $this->translator->trans("New document for validation"),
                    $this->translator->trans("The Operator ") . $operateur .  $this->translator->trans(" has sent to you a new document . Please click on this notification to display the content."),
                    $userReference[0],
                    $event->getDocumentOperateur()->getDemandeurId()->getId(),
                    'app_validation_circuit_document',
                    "DOCUMENT_OPERATOR",
                    $idCircuit->getId()
                );
            } else {
                //Recherche du mail Responsable Service
                $directionReference = $this->registry->getRepository(Direction::class)->find($idCircuit->getCodeDirection());
                $userReference = $this->registry->getRepository(User::class)->findBy(['email'=>$directionReference->getEmailPersonneRessource()]);
                //dd($userReference);
                $this->utils->envoiNotification(
                    $this->registry,
                    $this->translator->trans("New document for validation"),
                    $this->translator->trans("The Operator ") . $operateur .  $this->translator->trans(" has sent to you a new document . Please click on this notification to display the content."),
                    $userReference[0],
                    $event->getDocumentOperateur()->getDemandeurId()->getId(),
                    'app_validation_circuit_document',
                    "DOCUMENT_OPERATOR",
                    $idCircuit->getId()
                );

            }
        }

        /*Fin Notification*/
    }

    public function onDemandeOperateurAdd(
        AddDemandeOperateurEvent $event
    ){
        try {
            $modele = new  ModeleCommunication();

            //dd($user);
            //Recherche le modèle Actif
            $modeles = $this->doctrine->getRepository(ModeleCommunication::class)->findBy(['statut' => 'ACTIF', 'code_type_modele_communication' => 1]);
            $modele = $modeles[0];

            //Recherche le circuit du modèle actif
            $details_modele = $this->doctrine->getRepository(DetailsModele::class)->findBy(['code_modele' => $modele]);


            // ------------ Parcours du circuit modèle et Enregistrepment Copie du circuit de la demande -------------------------
            $i = 0;
            $dateCreation = new \DateTimeImmutable();
            foreach ($details_modele as $detail) {
                $i++;
                $circuit = new  CircuitCommunication();

                $circuit->setCodeModele($modele);
                $circuit->setCodeDirection($detail->getCodeDirection());
                $circuit->setTypeService($detail->getTypeService());
                $circuit->setCodeService($detail->getCodeService());
                $circuit->setCodeDemandeOperateur($event->getDemandeOperateur());
                $circuit->setNumSeq($detail->getNumseq());
                $circuit->setCreatedBy($event->getDemandeOperateur()->getCreatedBy());
                $circuit->setCreatedAt($dateCreation);

                $operateur = "";

                if ($event->getDemandeOperateur()->getCodeOperateur()->getId() == 2) {
                    $operateur = $this->registry->getRepository(Exploitant::class)->find($event->getDemandeOperateur()->getDemandeur()->getCodeexploitant())->getRaisonSocialeExploitant();
                } elseif ($event->getDemandeOperateur()->getCodeOperateur()->getId() == 3) {
                    $operateur = $this->registry->getRepository(Usine::class)->find($event->getDemandeOperateur()->getDemandeur()->getCodeindustriel())->getRaisonSocialeUsine();
                } elseif ($event->getDemandeOperateur()->getCodeOperateur()->getId() == 4) {
                    $operateur = $this->registry->getRepository(Exportateur::class)->find($event->getDemandeOperateur()->getDemandeur()->getCodeExportateur())->getRaisonSocialeExportateur();
                }

                $circuit->setOperateur($operateur);
                $userReference = new User();
                if ($i == 1) {
                    $circuit->setStatut("EN COURS");

                }

                $manager = $this->doctrine->getManager();
                $manager->persist($circuit);
                $manager->flush();
            }
            /*Recherche de l'ID du premier circuit a partir du code document*/
            $idCircuit = $this->communicationRepository->findBy(['code_demande_operateur' => $event->getDemandeOperateur()])[0];
            if ($idCircuit){
                /*Début Notification*/
                if ($idCircuit->getTypeService() == 'SERVICE') {
                    //Recherche du mail Responsable Service
                    $serviceReference = $this->registry->getRepository(ServiceMinef::class)->find($idCircuit->getCodeService());
                    $userReference = $this->registry->getRepository(User::class)->findBy(['email' => $serviceReference->getEmailPersonneRessource()]);

                    $this->utils->envoiNotification(
                        $this->registry,
                        $this->translator->trans("New document request for validation"),
                        $this->translator->trans("The Operator ") . $operateur . $this->translator->trans(" has sent to you a new statical document request . Please click on this notification to display the content."),
                        $userReference[0],
                        $event->getDemandeOperateur()->getDemandeur()->getId(),
                        'app_validation_circuit_demande',
                        "DEMANDE_OPERATOR",
                        $idCircuit->getId()
                    );
                } else {
                    //Recherche du mail Responsable Service
                    $directionReference = $this->registry->getRepository(Direction::class)->find($idCircuit->getCodeDirection());
                    $userReference = $this->registry->getRepository(User::class)->findBy(['email' => $directionReference->getEmailPersonneRessource()]);
                    $this->utils->envoiNotification(
                        $this->registry,
                        $this->translator->trans("New document for validation"),
                        $this->translator->trans("The Operator ") . $operateur . $this->translator->trans(" has sent to you a new document . Please click on this notification to display the content."),
                        $userReference[0],
                        $event->getDemandeOperateur()->getDemandeur()->getId(),
                        'app_validation_circuit_demande',
                        "DEMANDE_OPERATOR",
                        $idCircuit->getId()
                    );

                }
            }

        }   catch (\Throwable $exception){
            }
    }
}