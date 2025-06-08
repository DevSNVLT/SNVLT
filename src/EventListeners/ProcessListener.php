<?php

namespace App\EventListeners;

use App\Controller\Services\Utils;
use App\Entity\Admin\Exercice;
use App\Entity\Administration\DocStatsGen;
use App\Entity\References\PageDocGen;
use App\Events\Administration\AddNotificationEvent;
use App\Events\Administration\AddStockDocEvent;
use App\Repository\References\PageDocGenRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProcessListener
{
    private  $pageDocGenRepository;
    private $translator;
    public function __construct(private ManagerRegistry $doctrine, private LoggerInterface $logger, PageDocGenRepository $pageDocGenRepository,TranslatorInterface $translator, private Utils $utils)
    {
        $this->pageDocGenRepository = $pageDocGenRepository;
        $this->translator = $translator;
    }

    public function onStockDocAdd(
        AddStockDocEvent $event

        )

    {
        //$exercice_encours = new Exercice();
        $exercice_encours = $this->doctrine->getRepository(Exercice::class)->findLastExercice();
        $manager = $this->doctrine->getManager();
        $exercice = substr($exercice_encours, -2);
        $docname = $event->getStockdoc()->getLettre();
        $nb_pages = $event->getStockdoc()->getCodeTypeDocStat()->getNbPages(); //$statistiqueRepository->findOneBy(['abv'=>$docname]);
        if (!$nb_pages){
            $nb_pages = 10;
        }
        /* Création des documents */
        for($i= 1; $i< $event->getStockdoc()->getQte() + 1; $i++){


            $docstatgen = new  DocStatsGen();


            if (!$this->doctrine->getRepository(DocStatsGen::class)->findLastDocNumber($event->getStockdoc()->getCodeTypeDocStat()->getId())){
                $dernierePageDoc = 0;
            } else {
                $dernierePageDoc = $this->doctrine->getRepository(DocStatsGen::class)->findLastDocNumber($event->getStockdoc()->getCodeTypeDocStat()->getId());
            }


            $docstatgen->setDocname($event->getStockdoc()->getCodeTypeDocStat());
            $docstatgen->setLettre($docname);
            $docstatgen->setNumdoc($dernierePageDoc + 1);
            $docstatgen->setAnnee($exercice);
            $taille_doc = $dernierePageDoc + 1;
            switch (strlen($taille_doc)){
                case 1:
                    $taille_doc = "0000".$taille_doc;
                    break;
                case 2:
                    $taille_doc = "000".$taille_doc;
                    break;
                case 3:
                    $taille_doc = "00".$taille_doc;
                    break;
                case 4:
                    $taille_doc = "0".$taille_doc;
                    break;
                case 5:
                    $taille_doc = $taille_doc;
                    break;
                case 6:
                    $taille_doc = $taille_doc;
                    break;
            }
            $docstatgen->setUniqueDoc($this->uniqidReal(30).$docname.$taille_doc.$this->uniqidReal(23).$event->getStockdoc()->getCodeTypeDocStat()->getAbv().$this->uniqidReal(25));
            $docstatgen->setNumeroDoc($event->getStockdoc()->getLettre(). ' '. $event->getStockdoc()->getCodeTypeDocStat()->getAbv() . ' '. $exercice .' / ' . $taille_doc);
            $docstatgen->setUpdatedBy($event->getStockdoc()->getUpdatedBy());
            $docstatgen->setUpdatedAt($event->getStockdoc()->getUpdatedAt());
            $docstatgen->setCreatedBy($event->getStockdoc()->getCreatedBy());
            $docstatgen->setCreatedAt($event->getStockdoc()->getCreatedAt());
            $docstatgen->setCodeTypeDoc($event->getStockdoc());
            $docstatgen->setAttribue(false);

            $manager->persist($docstatgen);
            //$manager->flush();



            /*Enregistrement des pages du document*/
            $dateEnregistrement = new \DateTimeImmutable();
            for($j= 1; $j< $nb_pages + 1; $j++){
                $pagedoc = new PageDocGen();

                if (!$this->doctrine->getRepository(PageDocGen::class)->findLastPage($event->getStockdoc()->getCodeTypeDocStat()->getId())){
                    $dernierePage = 0;
                } else {
                    $dernierePage = $this->doctrine->getRepository(PageDocGen::class)->findLastPage($event->getStockdoc()->getCodeTypeDocStat()->getId());
                }


                $pagedoc->setDoctype($event->getStockdoc()->getCodeTypeDocStat()->getId());

                $pagedoc->setNumeroPage($j);
                $pagedoc->setSeqPage($dernierePage + 1);
                $taille = $dernierePage + 1;
                switch (strlen($taille)){
                    case 1:
                        $pagedoc->setNumpage("0000".$taille);
                        break;
                    case 2:
                        $pagedoc->setNumpage("000".$taille);
                        break;
                    case 3:
                        $pagedoc->setNumpage("00".$taille);
                        break;
                    case 4:
                        $pagedoc->setNumpage("0".$taille);
                        break;
                    case 5:
                        $pagedoc->setNumpage($taille);
                        break;
                    case 6:
                        $pagedoc->setNumpage($taille);
                        break;
                }

                $pagedoc->setCodeDocGen($docstatgen);
                $pagedoc->setCreatedAt($dateEnregistrement);
                $pagedoc->setUpdatedAt($dateEnregistrement);
                $pagedoc->setUpdatedBy($event->getStockdoc()->getCreatedBy());
                $pagedoc->setCreatedBy($event->getStockdoc()->getCreatedBy());

                $manager->persist($pagedoc);
                $manager->flush();
            }


             //sleep(1);
            $this->logger->info($this->translator->trans("The document "). $event->getStockdoc()->getLettre().'23 / ' . $i . $this->translator->trans(" has been generated successfully"));
        }

    }



    public function generatePage($docStatsGen, $nbPages,){

    }


    public  function uniqidReal($lenght) {
        // uniqid gives 13 chars, but you could adjust it to your needs.
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($lenght / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
        } else {
            throw new Exception("no cryptographically secure random function available");
        }
        return substr(bin2hex($bytes), 0, $lenght);
    }



    public function onRespoAdd(AddNotificationEvent $event){

        $sujet = $this->translator->trans('SNVLT REGISTRATION');
        $structure = "";
        if($event->getUser()->getCodeOperateur()->getId() == 2)
        {
            $structure = $event->getUser()->getCodeexploitant()->getRaisonSocialeExploitant();
        } elseif($event->getUser()->getCodeOperateur()->getId() == 3)
        {
            $structure = $event->getUser()->getCodeindustriel()->getRaisonSocialeUsine();
        } elseif($event->getUser()->getCodeOperateur()->getId() == 4)
        {
            $structure = $event->getUser()->getCodeExportateur()->getRaisonSocialeExportateur();
        } elseif($event->getUser()->getCodeOperateur()->getId() == 1)
        {
                    if($event->getUser()->getCodeService()){
                        $structure = $event->getUser()->getCodeService()->getLibelleService();
                    } elseif($event->getUser()->getCodeDirection()){
                        $structure = $event->getUser()->getCodeDirection()->getDenomination();
                    }else{
                        $structure = "MINEF";
                    }

        } elseif($event->getUser()->getCodeOperateur()->getId() == 5)
        {
            $structure = $event->getUser()->getCodeDr()->getDenomination();
        } elseif($event->getUser()->getCodeOperateur()->getId() == 6)
        {
            $structure = $event->getUser()->getCodeDdef()->getNomDdef();
        } elseif($event->getUser()->getCodeOperateur()->getId() == 7)
        {
            $structure = $event->getUser()->getCodeCantonnement()->getNomCantonnement();
        } elseif($event->getUser()->getCodeOperateur()->getId() == 10)
        {
            $structure = $event->getUser()->getCodePosteControle()->getDenomination();
        }else{
            $structure = $this->translator->trans("NOT DEFINED");
        }
        $description = $this->translator->trans('You have been added as ').$event->getUser()->getCodeOperateur()->getLibelleOperateur(). $this->translator->trans("  manager ").$this->translator->trans(" of the entity") . $structure .$this->translator->trans(' WELCOME');

        $from_user = $event->getUser()->getId();
        $to_user = $event->getUser();

        $this->utils->envoiNotification(
            $this->doctrine,
            $sujet,
            $description,
            $to_user,
            $from_user,
            "app_my_users",
            "PROFILE",
            $event->getUser()->getId()
        );
        $description = $this->translator->trans("Hi! Please for more security, Don't forget to reset your password");
        $sujet = $this->translator->trans('SNVLT SECURITY');
        $this->utils->envoiNotification(
            $this->doctrine,
            $sujet,
            $description,
            $to_user,
            $from_user,
            "app_forgot_password_request",
            "PROFILE",
            $event->getUser()->getId()
            );
    }

}