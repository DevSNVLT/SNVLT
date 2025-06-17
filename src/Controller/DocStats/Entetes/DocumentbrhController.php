<?php

namespace App\Controller\DocStats\Entetes;

use App\Controller\Services\AdministrationService;
use App\Controller\Services\synchro\Mouvements\Documents\Documentbrh\DocumentbrhSnvlt1;
use App\Controller\Services\Utils;
use App\Entity\Admin\Exercice;
use App\Entity\Administration\FicheProspection;
use App\Entity\Administration\ProspectionTemp;
use App\Entity\Autorisation\Attribution;
use App\Entity\Autorisation\AttributionPv;
use App\Entity\Autorisation\AutorisationPv;
use App\Entity\Autorisation\ContratBcbgfh;
use App\Entity\Autorisation\Reprise;
use App\Entity\DocStats\Entetes\Documentbcbgfh;
use App\Entity\DocStats\Entetes\Documentbcbp;
use App\Entity\DocStats\Entetes\Documentbrh;
use App\Entity\DocStats\Entetes\Documentcp;
use App\Entity\DocStats\Pages\Pagebcbgfh;
use App\Entity\DocStats\Pages\Pagebcbp;
use App\Entity\DocStats\Pages\Pagebrh;
use App\Entity\DocStats\Pages\Pagecp;
use App\Entity\DocStats\Saisie\Lignepagebcbgfh;
use App\Entity\DocStats\Saisie\Lignepagebcbp;
use App\Entity\DocStats\Saisie\Lignepagebrh;
use App\Entity\DocStats\Saisie\Lignepagecp;
use App\Entity\References\Cantonnement;
use App\Entity\References\SousPrefecture;
use App\Entity\References\Essence;
use App\Entity\References\Exploitant;
use App\Entity\References\Foret;
use App\Entity\References\PosteForestier;
use App\Entity\References\TypeForet;
use App\Entity\References\Usine;
use App\Entity\References\ZoneHemispherique;
use App\Entity\User;
use App\Events\Administration\AddFicheProspectionEvent;
use App\Form\Administration\FicheProspectionType;
use App\Form\DocStats\Pages\PagebrhType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\DocStats\Entetes\DocumentbrhRepository;
use App\Repository\DocStats\Pages\PagebrhRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use mysql_xdevapi\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class DocumentbrhController extends AbstractController
{

    public function __construct(
        private ManagerRegistry $m,
        private Utils $utils,
        private AdministrationService $administrationService,
        private SluggerInterface $slugger,
        private  DocumentbrhSnvlt1 $snvlt1)
    {
    }

    #[Route('/doc/stats/entetes/docbrh', name: 'app_op_docbrh')]
    public function index(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentbrhRepository $docs_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE') )
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                return $this->render('doc_stats/entetes/documentbrh/index.html.twig', [
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'groupe'=>$code_groupe,
                    'liste_parent'=>$permissions,
                    'usines_dest'=>$registry->getRepository(Usine::class)->findOnlyManager(),
                    'liste_forets'=>$registry->getRepository(Foret::class)->findBy(['code_type_foret'=>$registry->getRepository(TypeForet::class)->find(1)], ['numero_foret'=>'ASC']),
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/doc/stats/entetes/docbrh/valid_respo', name: 'validation_respo')]
    public function validation_respo(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE')  )
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                return $this->render('doc_stats/entetes/documentbrh/validation_respo.html.twig', [
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'groupe'=>$code_groupe,
                    'liste_parent'=>$permissions
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    #[Route('/snvlt/brh/validation_chargement_respo', name: 'app_validation_chargements')]
    public function app_validation_chargements(
        Request $request,
        UserRepository $userRepository,
        PagebrhRepository $page_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                //Liste des pages ou chargements à valider
                $liste_chargements = array();

                // Les chargements concernent des BCBGFH-PEF
                $attributions = $registry->getRepository(Attribution::class)->findBy(['code_exploitant'=>$user->getCodeexploitant()]);

                foreach($attributions as $attribution){
                    $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution]);
                    foreach ($reprises as $reprise){
                        $documentsbrhs = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$reprise]);
                        foreach ($documentsbrhs as $documentbrh){
                            $page_brh = $registry->getRepository(Pagebrh::class)->findBy(['fini'=>false, 'soumettre'=>true, 'code_docbrh'=>$documentbrh]);
                            foreach($page_brh as $page){

                                $billes = $registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$page]);
                                $nb_biolles = 0;
                                $volume = 0;

                                foreach($billes as $bille){
                                    $nb_biolles = $nb_biolles + 1;
                                    $volume = $volume + $bille->getCubageLignepagebrh();
                                }
                                $usine = "-";
                                if($page->getParcUsineBrh()){
                                    if ($page->getParcUsineBrh()->getSigle()){
                                        $usine = $page->getParcUsineBrh()->getSigle();
                                    } else {
                                        $usine = $page->getParcUsineBrh()->getRaisonSocialeUsine();
                                    }
                                }

                                $liste_chargements[] = array(
                                    'id_page'=>$page->getId(),
                                    'type_doc'=>'BCBGFH',
                                    'numero_page'=>$page->getNumeroPagebrh(),
                                    'numero_brh'=>$page->getCodeDocbrh()->getNumeroDocbrh(),
                                    'foret'=>$page->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination(),
                                    'destination_brh'=>$page->getDestinationPagebrh(),
                                    'usine_dest'=>$usine,
                                    'date_chargement'=>$page->getDateChargementbrh()->format('d m Y'),
                                    'nb_billes'=>$nb_biolles,
                                    'volume_brh'=>round($volume, 3),
                                    'immat'=>$page->getImmatcamion(),
                                    'conducteur'=>$page->getChauffeurbrh()
                                );
                            }
                        }
                    }
                }

                // Les chargements concernent des BCBP

                    $reprises = $registry->getRepository(AutorisationPv::class)->findBy(['code_exploitant'=>$user->getCodeexploitant()]);
                    foreach ($reprises as $reprise){
                        $documentsbcbps = $registry->getRepository(Documentbcbp::class)->findBy(['code_autorisation_pv'=>$reprise]);
                        foreach ($documentsbcbps as $docbcbp){
                            $page_bcbp = $registry->getRepository(Pagebcbp::class)->findBy(['fini'=>false, 'soumettre'=>true, 'code_docbcbp'=>$docbcbp]);
                            foreach($page_bcbp as $page){

                                $billes = $registry->getRepository(Lignepagebcbp::class)->findBy(['code_pagebcbp'=>$page]);
                                $nb_biolles = 0;
                                $volume = 0;

                                foreach($billes as $bille){
                                    $nb_biolles = $nb_biolles + 1;
                                    $volume = $volume + $bille->getVolume();
                                }
                                $usine = "-";
                                if($page->getParcUsine()){
                                    if ($page->getParcUsine()->getSigle()){
                                        $usine = $page->getParcUsine()->getSigle();
                                    } else {
                                        $usine = $page->getParcUsine()->getRaisonSocialeUsine();
                                    }
                                }

                                $liste_chargements[] = array(
                                    'id_page'=>$page->getId(),
                                    'type_doc'=>'BCBP',
                                    'numero_page'=>$page->getNumeroPagebcbp(). " [". $page->getEssence()->getNomVernaculaire(). "]",
                                    'numero_brh'=>$page->getCodeDocbcbp()->getNumeroDocbcbp(),
                                    'foret'=>$page->getCodeDocbcbp()->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getDenomination(),
                                    'destination_brh'=>$page->getDestination(),
                                    'usine_dest'=>$usine,
                                    'date_chargement'=>$page->getDateChargement()->format('d m Y'),
                                    'nb_billes'=>$nb_biolles,
                                    'volume_brh'=>round($volume, 3),
                                    'immat'=>$page->getImmatcamion(),
                                    'conducteur'=>$page->getConducteur(). " - [". $page->getTransporteur()."]"
                                );
                            }
                        }
                    }

                // Les chargements concernent des BCBGFH-FC

                $contrats = $registry->getRepository(ContratBcbgfh::class)->findBy(['code_exploitant'=>$user->getCodeexploitant()]);
                foreach ($contrats as $contrat){
                    $documentbcbgfcs = $registry->getRepository(Documentbcbgfh::class)->findBy(['code_contrat'=>$contrat]);
                    foreach ($documentbcbgfcs as $documentbcbgfc){
                        $page_bcbgfhs = $registry->getRepository(Pagebcbgfh::class)->findBy(['fini'=>false, 'soumettre'=>true, 'code_docbcbgfh'=>$documentbcbgfc]);
                        foreach($page_bcbgfhs as $page){

                            $billes = $registry->getRepository(Lignepagebcbgfh::class)->findBy(['code_pagebcbgfh'=>$page]);
                            $nb_billes = 0;
                            $volume = 0;

                            foreach($billes as $bille){
                                $nb_billes = $nb_billes + 1;
                                $volume = $volume + $bille->getCubageLignepagebcbgfh();
                            }
                            $usine = "-";
                            if($page->getParcUsineBcbgfh()){
                                if ($page->getParcUsineBcbgfh()->getSigle()){
                                    $usine = $page->getParcUsineBcbgfh()->getSigle();
                                } else {
                                    $usine = $page->getParcUsineBcbgfh()->getRaisonSocialeUsine();
                                }
                            }

                            $liste_chargements[] = array(
                                'id_page'=>$page->getId(),
                                'type_doc'=>'BCBGFH-FC',
                                'numero_page'=>$page->getNumeroPagebcbgfh(),
                                'numero_brh'=>$page->getCodeDocbcbgfh()->getNumeroDocbcbgfh(),
                                'foret'=>$page->getCodeDocbcbgfh()->getCodeContrat()->getCodeForet()->getDenomination(),
                                'destination_brh'=>$page->getDestinationPagebcbgfh(),
                                'usine_dest'=>$usine,
                                'date_chargement'=>$page->getDateChargementbcbgfh()->format('d m Y'),
                                'nb_billes'=>$nb_billes,
                                'volume_brh'=>round($volume, 3),
                                'immat'=>$page->getImmatcamion(),
                                'conducteur'=>$page->getChauffeurbcbgfh()
                            );
                        }
                    }
                }
                    rsort($liste_chargements);



                return  new JsonResponse(json_encode($liste_chargements));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    #[Route('snvlt/docbrh/op/{id_foret}', name: 'app_docs_brh_json')]
    public function my_doc_brh(
        Request $request,
        UserRepository $userRepository,
        int $id_foret,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE') )
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $exo = $request->getSession()->get("exercice");
                $exercice = $registry->getRepository(Exercice::class)->find((int) $exo);

                $mes_docs_brh = array();

                //------------------------- Filtre les brh par type Opérateur ------------------------------------- //

                //------------------------- Filtre les brh ADMIN ------------------------------------- //
                if($user->getCodeGroupe()->getId() == 1 or $this->isGranted('ROLE_DPIF_SAISIE') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_DPIF')){
                    if ($id_foret == 0){
                        $documents_brh = $registry->getRepository(Documentbrh::class)->findBy(['exercice'=>$exercice]);
                        foreach ($documents_brh as $document_brh){

                            if ($document_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()){
                                $canton = $document_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getNomCantonnement();
                                $d = $document_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getCodeDr()->getDenomination();
                            }else {
                                $canton = "-";
                                $d = "-";
                            }
                            $sigle ="-";
                            if ($document_brh->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getSigle()){
                                $sigle =  $document_brh->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getSigle();
                            }

                            $mes_docs_brh[] = array(
                                'id_document_brh'=>$document_brh->getId(),
                                'numero_docbrh'=>$document_brh->getNumeroDocbrh(),
                                'foret'=>$document_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination(),
                                'cantonnement'=>$canton,
                                'dr'=>$d,
                                'date_delivrance'=>$document_brh->getDelivreDocbrh()->format("d m Y"),
                                'etat'=>$document_brh->isEtat(),
                                'exploitant'=>$document_brh->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getRaisonSocialeExploitant(),
                                'sigle'=>$sigle,
                                'code_exploitant'=>$document_brh->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getNumeroExploitant(),
                                'volume_brh'=>round($this->getVolumebrh($document_brh), 3),
                                'nb_billes'=>$this->getNbBilles($document_brh),
                                'nb_arbres'=>$this->getNbArbres($document_brh),
                                'nb_pages'=>$this->getNombrePage($document_brh)
                            );
                        }
                    } else {
                        $foret_selectionnee = $registry->getRepository(Foret::class)->find($id_foret);
                        if ($foret_selectionnee){
                            $attributions_foret = $registry->getRepository(Attribution::class)->findBy([
                                'code_foret'=>$foret_selectionnee
                            ]);
                            foreach ($attributions_foret as $att){
                                $reprises_foret = $registry->getRepository(Reprise::class)->findBy([
                                    'code_attribution'=>$att
                                ]);
                                foreach ($reprises_foret as $reprise_f){
                                    $documents_brh = $registry->getRepository(Documentbrh::class)->findBy(['exercice'=>$exercice, 'code_reprise'=>$reprise_f]);
                                    foreach ($documents_brh as $document_brh){

                                        if ($document_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()){
                                            $canton = $document_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getNomCantonnement();
                                            $d = $document_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getCodeDr()->getDenomination();
                                        }else {
                                            $canton = "-";
                                            $d = "-";
                                        }
                                        $sigle ="-";
                                        if ($document_brh->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getSigle()){
                                            $sigle =  $document_brh->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getSigle();
                                        }

                                        $mes_docs_brh[] = array(
                                            'id_document_brh'=>$document_brh->getId(),
                                            'numero_docbrh'=>$document_brh->getNumeroDocbrh(),
                                            'foret'=>$document_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination(),
                                            'cantonnement'=>$canton,
                                            'dr'=>$d,
                                            'date_delivrance'=>$document_brh->getDelivreDocbrh()->format("d m Y"),
                                            'etat'=>$document_brh->isEtat(),
                                            'exploitant'=>$document_brh->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getRaisonSocialeExploitant(),
                                            'sigle'=>$sigle,
                                            'code_exploitant'=>$document_brh->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getNumeroExploitant(),
                                            'volume_brh'=>round($this->getVolumebrh($document_brh), 3),
                                            'nb_billes'=>$this->getNbBilles($document_brh),
                                            'nb_arbres'=>$this->getNbArbres($document_brh),
                                            'nb_pages'=>$this->getNombrePage($document_brh)
                                        );
                                    }
                                }
                            }
                        }

                    }

                    //------------------------- Filtre les brh DR ------------------------------------- //
                } else {
                    if ($user->getCodeDr()){
                        //dd($user->getCodeDr());
                        $cantonnements = $registry->getRepository(Cantonnement::class)->findBy(['code_dr'=>$user->getCodeDr()]);
                        foreach ($cantonnements as $cantonnement){
                            $forets = $registry->getRepository(Foret::class)->findBy(['code_cantonnement'=>$cantonnement]);

                            foreach ($forets as $foret){
                                $attributions = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$foret,'reprise'=>true,  'exercice'=>$exercice, 'statut'=>true]);
                                foreach ($attributions as $attribution){
                                    $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution, 'statut'=>true]);
                                    foreach ($reprises as $reprise){
                                        $documents_brh = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$reprise]);
                                        foreach ($documents_brh as $document_brh){

                                            if ($document_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()){
                                                $canton = $document_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getNomCantonnement();
                                                $d = $document_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getCodeDr()->getDenomination();
                                            }else {
                                                $canton = "-";
                                                $d = "-";
                                            }
                                            $sigle ="-";
                                            if ($document_brh->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getSigle()){
                                                $sigle =  $document_brh->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getSigle();
                                            }
                                            $mes_docs_brh[] = array(
                                                'id_document_brh'=>$document_brh->getId(),
                                                'numero_docbrh'=>$document_brh->getNumeroDocbrh(),
                                                'foret'=>$document_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination(),
                                                'cantonnement'=>$canton,
                                                'dr'=>$d,
                                                'sigle'=>$sigle,
                                                'date_delivrance'=>$document_brh->getDelivreDocbrh()->format("d m Y"),
                                                'etat'=>$document_brh->isEtat(),
                                                'attribution_attribue'=>$document_brh->getCodeReprise()->getCodeAttribution()->isStatut(),
                                                'reprise_attribue'=>$document_brh->getCodeReprise()->getCodeAttribution()->isReprise(),
                                                'exploitant'=>$document_brh->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getRaisonSocialeExploitant(),
                                                'code_exploitant'=>$document_brh->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getNumeroExploitant(),
                                                'volume_brh'=>round($this->getVolumebrh($document_brh), 3),
                                                'nb_billes'=>$this->getNbBilles($document_brh),
                                                'nb_arbres'=>$this->getNbArbres($document_brh),
                                                'nb_pages'=>$this->getNombrePage($document_brh)
                                            );
                                        }

                                    }
                                }
                            }
                        }

                        //------------------------- Filtre les brh DD ------------------------------------- //
                    } elseif ($user->getCodeDdef()){
                        $cantonnements = $registry->getRepository(Cantonnement::class)->findBy(['code_ddef'=>$user->getCodeDdef()]);
                        foreach ($cantonnements as $cantonnement){
                            $forets = $registry->getRepository(Foret::class)->findBy(['code_cantonnement'=>$cantonnement]);
                            foreach ($forets as $foret){
                                $attributions = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$user->getCodeexploitant(),'reprise'=>true, 'exercice'=>$exercice, 'statut'=>true]);
                                foreach ($attributions as $attribution){
                                    $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution, 'statut'=>true]);
                                    foreach ($reprises as $reprise){
                                        $documents_brh = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$reprise]);
                                        foreach ($documents_brh as $document_brh){
                                            if ($document_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()){
                                                $canton = $document_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getNomCantonnement();
                                                $d = $document_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getCodeDr()->getDenomination();
                                            }else {
                                                $canton = "-";
                                                $d = "-";
                                            }
                                            $sigle ="-";
                                            if ($document_brh->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getSigle()){
                                                $sigle =  $document_brh->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getSigle();
                                            }
                                            $mes_docs_brh[] = array(
                                                'id_document_brh'=>$document_brh->getId(),
                                                'numero_docbrh'=>$document_brh->getNumeroDocbrh(),
                                                'foret'=>$document_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination(),
                                                'cantonnement'=>$canton,
                                                'dr'=>$d,
                                                'sigle'=>$sigle,
                                                'date_delivrance'=>$document_brh->getDelivreDocbrh()->format("d m Y"),
                                                'etat'=>$document_brh->isEtat(),
                                                'attribution_attribue'=>$document_brh->getCodeReprise()->getCodeAttribution()->isStatut(),
                                                'reprise_attribue'=>$document_brh->getCodeReprise()->getCodeAttribution()->isReprise(),
                                                'exploitant'=>$document_brh->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getRaisonSocialeExploitant(),
                                                'code_exploitant'=>$document_brh->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getNumeroExploitant(),
                                                'volume_brh'=>round($this->getVolumebrh($document_brh), 3),
                                                'nb_billes'=>$this->getNbBilles($document_brh),
                                                'nb_arbres'=>$this->getNbArbres($document_brh),
                                                'nb_pages'=>$this->getNombrePage($document_brh)
                                            );
                                        }

                                    }
                                }
                            }
                        }

                        //------------------------- Filtre les brh CANTONNEMENT ------------------------------------- //
                    } elseif ($user->getCodeCantonnement()){
                        $forets = $registry->getRepository(Foret::class)->findBy(['code_cantonnement'=>$user->getCodeCantonnement()]);

                        foreach ($forets as $foret){
                            $attributions = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$foret,'reprise'=>true, 'exercice'=>$exercice, 'statut'=>true]);
                            foreach ($attributions as $attribution){
                                $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution, 'statut'=>true]);
                                foreach ($reprises as $reprise){
                                    $documents_brh = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$reprise]);
                                    foreach ($documents_brh as $document_brh){
                                        if ($document_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()){
                                            $canton = $document_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getNomCantonnement();
                                            $d = $document_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getCodeDr()->getDenomination();
                                        }else {
                                            $canton = "-";
                                            $d = "-";
                                        }
                                        $sigle ="-";
                                        if ($document_brh->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getSigle()){
                                            $sigle =  $document_brh->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getSigle();
                                        }
                                        $mes_docs_brh[] = array(
                                            'id_document_brh'=>$document_brh->getId(),
                                            'numero_docbrh'=>$document_brh->getNumeroDocbrh(),
                                            'foret'=>$document_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination(),
                                            'cantonnement'=>$canton,
                                            'dr'=>$d,
                                            'sigle'=>$sigle,
                                            'date_delivrance'=>$document_brh->getDelivreDocbrh()->format("d m Y"),
                                            'etat'=>$document_brh->isEtat(),
                                            'attribution_attribue'=>$document_brh->getCodeReprise()->getCodeAttribution()->isStatut(),
                                            'reprise_attribue'=>$document_brh->getCodeReprise()->getCodeAttribution()->isReprise(),
                                            'exploitant'=>$document_brh->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getRaisonSocialeExploitant(),
                                            'code_exploitant'=>$document_brh->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getNumeroExploitant(),
                                            'volume_brh'=>round($this->getVolumebrh($document_brh), 3),
                                            'nb_billes'=>$this->getNbBilles($document_brh),
                                            'nb_arbres'=>$this->getNbArbres($document_brh),
                                            'nb_pages'=>$this->getNombrePage($document_brh)
                                        );
                                    }

                                }
                            }
                        }

                        //------------------------- Filtre les brh POSTE CONTROLE ------------------------------------- //
                    } elseif ($user->getCodePosteControle()){
                        $forets = $registry->getRepository(Foret::class)->findBy(['code_cantonnement'=>$user->getCodePosteControle()->getCodeCantonnement()]);
                        foreach ($forets as $foret){
                            $attributions = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$user->getCodeexploitant(),'reprise'=>true, 'exercice'=>$exercice, 'statut'=>true]);
                            foreach ($attributions as $attribution){
                                $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution, 'statut'=>true]);
                                foreach ($reprises as $reprise){
                                    $documents_brh = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$reprise]);
                                    foreach ($documents_brh as $document_brh){

                                        if ($document_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()){
                                            $canton = $document_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getNomCantonnement();
                                            $d = $document_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getCodeDr()->getDenomination();
                                        }else {
                                            $canton = "-";
                                            $d = "-";
                                        }
                                        $sigle ="-";
                                        if ($document_brh->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getSigle()){
                                            $sigle =  $document_brh->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getSigle();
                                        }
                                        $mes_docs_brh[] = array(
                                            'id_document_brh'=>$document_brh->getId(),
                                            'numero_docbrh'=>$document_brh->getNumeroDocbrh(),
                                            'foret'=>$document_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination(),
                                            'cantonnement'=>$canton,
                                            'dr'=>$d,
                                            'sigle'=>$sigle,
                                            'date_delivrance'=>$document_brh->getDelivreDocbrh()->format("d m Y"),
                                            'etat'=>$document_brh->isEtat(),
                                            'attribution_attribue'=>$document_brh->getCodeReprise()->getCodeAttribution()->isStatut(),
                                            'reprise_attribue'=>$document_brh->getCodeReprise()->getCodeAttribution()->isReprise(),
                                            'exploitant'=>$document_brh->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getRaisonSocialeExploitant(),
                                            'code_exploitant'=>$document_brh->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getNumeroExploitant(),
                                            'volume_brh'=>round($this->getVolumebrh($document_brh), 3),
                                            'nb_billes'=>$this->getNbBilles($document_brh),
                                            'nb_arbres'=>$this->getNbArbres($document_brh),
                                            'nb_pages'=>$this->getNombrePage($document_brh)
                                        );
                                    }

                                }
                            }
                        }
                        //------------------------- Filtre les brh EXPLOITANT------------------------------------- //
                    } elseif ($user->getCodeexploitant()){
                        $attributions = $registry->getRepository(Attribution::class)->findBy(['code_exploitant'=>$user->getCodeexploitant(), 'reprise'=>true, 'statut'=>true,'exercice'=>$exercice]);
                        foreach ($attributions as $attribution){
                            $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution, 'statut'=>true]);

                            foreach ($reprises as $reprise){
                                $documents_brh = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$reprise, 'signature_cef'=>true, 'signature_dr'=>true],['created_at'=>'DESC']);
                                foreach ($documents_brh as $document_brh){
                                    if ($document_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()){
                                        $canton = $document_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getNomCantonnement();
                                        $d = $document_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getCodeDr()->getDenomination();
                                    }else {
                                        $canton = "-";
                                        $d = "-";
                                    }

                                    $mes_docs_brh[] = array(
                                        'id_document_brh'=>$document_brh->getId(),
                                        'numero_docbrh'=>$document_brh->getNumeroDocbrh(),
                                        'foret'=>$document_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination(),
                                        'cantonnement'=>$canton,
                                        'dr'=>$d,
                                        'date_delivrance'=>$document_brh->getDelivreDocbrh()->format("d m Y"),
                                        'etat'=>$document_brh->isEtat(),
                                        'volume_brh'=>round($this->getVolumebrh($document_brh), 3),
                                        'nb_billes'=>$this->getNbBilles($document_brh),
                                        'nb_arbres'=>$this->getNbArbres($document_brh),
                                        'nb_pages'=>$this->getNombrePage($document_brh)
                                    );
                                }

                            }

                        }
                    }


                }
                return new JsonResponse(json_encode($mes_docs_brh));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }



    }

    #[Route('/snvlt/docbrh/op/pages/{id_brh}', name: 'affichage_brh_json')]
    public function affiche_brh(
        Request $request,
        int $id_brh,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentbrhRepository $docs_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE') or $this->isGranted('ROLE_ADMINISTRATIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $numerodoc = "";

                $documentbrh = $registry->getRepository(Documentbrh::class)->find($id_brh);
                if($documentbrh){$numerodoc = $documentbrh->getNumeroDocbrh();}

                return $this->render('doc_stats/entetes/documentbrh/affiche_brh.html.twig', [
                    'document_name'=>$documentbrh,
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'groupe'=>$code_groupe,
                    'liste_parent'=>$permissions,
                    'essences'=>$registry->getRepository(Essence::class)->findBy([], ['nom_vernaculaire'=>'ASC']),
                    'zones'=>$registry->getRepository(ZoneHemispherique::class)->findBy([], ['zone'=>'ASC']),
                    'usines'=>$registry->getRepository(Usine::class)->findBy([], ['raison_sociale_usine'=>'ASC']),
                    'villes'=>$registry->getRepository(SousPrefecture::class)->findBy([], ['nom_sousprefecture'=>'ASC']),
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/docbrh/op/pages_brh/{id_brh}', name: 'affichage_pages_brh_json')]
    public function affiche_pages_brh(
        Request $request,
        int $id_brh,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentbrhRepository $docs_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE') or $this->isGranted('ROLE_ADMINISTRATIF'))
            {
                $doc_brh = $docs_brh->find($id_brh);
                if($doc_brh){
                    $pages_brh = $registry->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$doc_brh], ['id'=>'ASC']);
                    $my_brh_pages = array();

                    foreach ($pages_brh as $page){
                        $my_brh_pages[] = array(
                            'id_page'=>$page->getId(),
                            'numero_page'=>$page->getNumeroPagebrh()
                        );
                    }
                    return  new JsonResponse(json_encode($my_brh_pages));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/brh/validate_form/{id_page}', name: 'validate_page_brh_json')]
    public function validate_form_page_brh(
        Request $request,
        int $id_page,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        PagebrhRepository $page_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $page = $page_brh->find($id_page);
                if($page && !$page->isFini()){
                   if ($page->getLignepagebrhs()->count() > 0)
                   {
                       $page->setFini(true);
                       $page->setEntreLje(false);
                       $page->setSoumettre(true);

                       if ($this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE')){
                           $page->setConfirmationUsine(true);
                       }

                       $registry->getManager()->persist($page);

                        // Log SNVLT
                        $this->administrationService->save_action(
                            $user,
                            "PAGE_BCBGFH",
                            "VALIDATION CHARGEMENT",
                            new \DateTimeImmutable(),
                            "Le chargement N% ". $page->getNumeroPagebrh() . " du BCBGFH " . $page->getCodeDocbrh()->getNumeroDocbrh() . " vient d'être validé par l'agent " . $user . " de la structure [" . $page->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeexploitant()->getRaisonSocialeExploitant() . " ]. Chargement en partance pour l'usine ". $page->getParcUsineBrh()
                        );

                        //Notification App Respo Usine
                       if ($page->getParcUsineBrh()->getEmailPersonneRessource()){
                           $respo_usine = $registry->getRepository(User::class)->findOneBy([
                               'email'=>$page->getParcUsineBrh()->getEmailPersonneRessource()
                           ]);
                           if ($respo_usine){
                               $this->utils->envoiNotification(
                                   $registry,
                                   "Chargement N° ".  $page->getNumeroPagebrh() . " [" . $page->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination() . "] en destination de votre usine " ,
                                   "Chargement ". $page->getCodeDocbrh()->getTypeDocument()->getDenomination() . " N° " . $page->getCodeDocbrh()->getNumeroDocbrh() . " - Feuillet N° ". $page->getNumeroPagebrh() . " est en transit vers votre usine  ",
                                   $respo_usine,
                                   $user->getId(),
                                   "app_my_loadings_notifs",
                                   "PAGE_BCBGFH",
                                   $page->getId()
                               );
                           }

                       }


                       // Notification Email Respo Usine
                       $registry->getManager()->flush();

                       // Mise à jour dans SNVLT1
                       try {
                           $this->snvlt1->EditPageDocbrh($page);
                       }catch (\Throwable $exception){
                        $this->addFlash("Error","Erreur de Mise à Jour Pagebrh depuis SNVLT TAMPON");
                       }


                       return  new JsonResponse(json_encode(true));
                   } else {
                       return  new JsonResponse(json_encode(false));
                   }

                }else {
                    return  new JsonResponse(json_encode(false));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
    #[Route('/snvlt/brh/validate_usine/{id_page}', name: 'validate_page_brh_usine_json')]
    public function validate_page_brh_usine_json(
        Request $request,
        int $id_page,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        PagebrhRepository $page_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $page = $page_brh->find($id_page);
                if($page){
                    if ($page->getLignepagebrhs()->count() > 0)
                    {
                        $page->setFini(true);
                        $page->setEntreLje(false);
                        $page->setSoumettre(true);
                        $page->setConfirmationUsine(true);

                        $registry->getManager()->persist($page);

                        // Log TrackTimber
                        $this->administrationService->save_action(
                            $user,
                            "PAGE_BCBGFH",
                            "VALIDATION CHARGEMENT",
                            new \DateTimeImmutable(),
                            "Le chargement N% ". $page->getNumeroPagebrh() . " du BCBGFH " . $page->getCodeDocbrh()->getNumeroDocbrh() . " vient d'être validé par l'agent " . $user . " de la structure [" . $page->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeexploitant()->getRaisonSocialeExploitant() . " ]. Chargement en partance pour l'usine ". $page->getParcUsineBrh()
                        );

                        $registry->getManager()->flush();

                        // Mise à jour dans SNVLT1
                        try {
                            $this->snvlt1->EditPageDocbrh($page);
                        }catch (\Throwable $exception){
                            $this->addFlash("Error","Erreur de Mise à Jour Pagebrh depuis SNVLT TAMPON");
                        }

                        return  new JsonResponse(json_encode(true));
                    } else {
                        return  new JsonResponse(json_encode(false));
                    }

                }else {
                    return  new JsonResponse(json_encode(false));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
    #[Route('/snvlt/brh/soumettre_chargement/{id_page}', name: 'soumettre_chargement')]
    public function soumettre_chargement(
        Request $request,
        int $id_page,
        UserRepository $userRepository,
        PagebrhRepository $page_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $page = $page_brh->find($id_page);
                if($page && !$page->isFini()){
                    if ($page->getLignepagebrhs()->count() > 0)
                    {
                        $page->setSoumettre(true);
                        $page->setFini(false);
                        $page->setConfirmationUsine(false);
                        $page->setEntreLje(false);

                        $page->setExercice($this->administrationService->getAnnee()->getId());
                        $registry->getManager()->persist($page);


                        // Envoi d'une notification au responsable Usine Destination, à l'exploitant forestier et aux Opérateurs MINEF concernés
                        $emailRespoExploitant = $registry->getRepository(Exploitant::class)->find($page->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeExploitant())->getEmailPersonneRessource();

                        //Notif Responsable Structure forestière
                        if ($registry->getRepository(User::class)->findOneBy(['email'=>$emailRespoExploitant])){
                            $this->utils->envoiNotification(
                                $registry,
                                "Chargement Grumes N° ". $page->getNumeroPagebrh() . " à valider",
                                "Le chargement N% ". $page->getNumeroPagebrh() . " du BCBGFH " . $page->getCodeDocbrh()->getNumeroDocbrh() . " vient d'être enregistré pour approbation de votre part.'" . $user ,
                                $registry->getRepository(User::class)->findOneBy(['email'=>$emailRespoExploitant]),
                                $user->getId(),
                                "validation_respo",
                                "PAGE_BCBGFH",
                                $page->getId()
                            );
                        }


                        // Log SNVLT
                        $this->administrationService->save_action(
                            $user,
                            "PAGE_BCBGFH",
                            "APPROBATION CHARGEMENT",
                            new \DateTimeImmutable(),
                            "Le chargement N% ". $page->getNumeroPagebrh() . " du BCBGFH " . $page->getCodeDocbrh()->getNumeroDocbrh() . " vient d'être soumi par l'agent " . $user . " de la structure [" . $user->getCodeexploitant()->getRaisonSocialeExploitant() . " ] pour approbation à son responsable."
                        );

                        $registry->getManager()->flush();

                        // Enregistrement des données dans SNVLT1
                        try {
                            $this->snvlt1->EditPageDocbrh($page);
                        }catch (\Throwable $exception){
                            $this->addFlash("Error","Erreur de Mise à Jour Pagebrh depuis SNVLT TAMPON");
                        }

                        return  new JsonResponse(json_encode(true));
                    } else {
                        return  new JsonResponse(json_encode(false));
                    }

                }else {
                    return  new JsonResponse(json_encode(false));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/docbrh/op/pages_brh/data/{id_page}', name: 'affichage_page_data_brh_json')]
    public function affiche_page_courante(
        Request $request,
        int $id_page,
        UserRepository $userRepository,
        PagebrhRepository $pages_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE') or $this->isGranted('ROLE_ADMINISTRATIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $page_brh = $pages_brh->find($id_page);
                if($page_brh){
                    $parc_usine = "";
                    $_usine = "";
                    if($page_brh->getDateChargementbrh()) { $date_chargement = $page_brh->getDateChargementbrh()->format('Y-m-d');} else { $date_chargement = ""; }
                    //dd($page_brh->getDateChargementbrh()->format('d/m/Y'));
                    if($page_brh->getParcUsineBrh()) {
                        $parc_usine = $page_brh->getParcUsineBrh()->getId();
                        $_usine = $page_brh->getParcUsineBrh()->getSigle();
                        if(!$_usine or $_usine="NULL"){
                            $_usine = $page_brh->getParcUsineBrh()->getRaisonSocialeUsine();
                        }
                    }
                    $my_brh_page = array();
                    if ($page_brh->getPhoto()){
                        $photo = $page_brh->getPhoto();
                    } else {
                        $photo = "";
                    }
                    $sp = $registry->getRepository(SousPrefecture::class)->findOneBy(['nom_sousprefecture'=>$page_brh->getDestinationPagebrh()]);
                    if ($sp){
                       $destination =  $sp->getNomSousprefecture();
                       $dest_id = $sp->getId();
                    } else {
                        $destination =  "-";
                        $dest_id = 0;
                    }
                    $my_brh_page[] = array(
                        'id_page'=>$page_brh->getId(),
                        'numero_page'=>$page_brh->getNumeroPagebrh(),
                        'date_chargement'=>$date_chargement,
                        'destination'=>$destination,
                        'dest_id'=>$dest_id,
                        'parc_usine'=>$parc_usine,
                        'usine_dest'=>$_usine,
                        'transporteur'=>$page_brh->getChauffeurbrh(),
                        'cout'=>$page_brh->getCoutTransportbrh(),
                        'village'=>$page_brh->getVillagePagebrh(),
                        'immatriculation'=>$page_brh->getImmatcamion(),
                        'fini'=>$page_brh->isFini(),
                        'photo'=>$photo
                    );

                    return  new JsonResponse(json_encode($my_brh_page));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/docbrh/op/lignes_brh/data/{id_page}', name: 'affichage_ligne_brh_data_brh_json')]
    public function affiche_lignes_brh_courante(
        Request $request,
        int $id_page,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        PagebrhRepository $pages_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE') or $this->isGranted('ROLE_ADMINISTRATIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $page_brh = $pages_brh->find($id_page);
                if($page_brh){
                    $lignes_brh = $registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$page_brh]);
                    $my_brh_page = array();


                    foreach ($lignes_brh as $lignebrh){
						$zh = "-";
						$essence = "-";

						if($lignebrh->getZhLignepagebrh())
						{
							$zh = $lignebrh->getZhLignepagebrh()->getZone();
						}

						if($lignebrh->getNomEssencebrh())
						{
							$essence = $lignebrh->getNomEssencebrh()->getNomVernaculaire();
						}

                        $my_brh_page[] = array(
                            'id_ligne'=>$lignebrh->getId(),
                            'numero_ligne'=>$lignebrh->getNumeroLignepagebrh(),
                            'lettre'=>$lignebrh->getLettreLignepagebrh(),
                            'essence'=>$essence,
                            'x_brh'=>$lignebrh->getXLignepagebrh(),
                            'y_brh'=>$lignebrh->getYLignepagebrh(),
                            'zh_brh'=>$zh,
                            'lng_brh'=>$lignebrh->getLongeurLignepagebrh(),
                            'dm_brh'=>$lignebrh->getDiametreLignepagebrh(),
                            'cubage_brh'=>$lignebrh->getCubageLignepagebrh(),
                            'fini'=>$page_brh->isFini(),
                            'utilisateur'=>$lignebrh->getCreatedBy(),
                        );
                    }


                    return  new JsonResponse(json_encode($my_brh_page));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    function getVolumebrh(Documentbrh $documentbrh):float
    {
        $volumebrh = 0;
        if($documentbrh){
            $pagebrh =$this->m->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$documentbrh]);
            foreach ($pagebrh as $page){
                $lignepages = $this->m->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$page]);
                foreach ($lignepages as $ligne){
                    $volumebrh = $volumebrh +  $ligne->getCubageLignepagebrh();
                }
            }
            return $volumebrh;
        } else {
            return $volumebrh;
        }
    }
    function getNombrePage(Documentbrh $documentbrh):float
        {

            $nb_pages = 0;
            if($documentbrh){
                $pagebrh =$this->m->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$documentbrh]);
                foreach ($pagebrh as $page){
                    $volumebrh = 0;
                    $lignepages = $this->m->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$page]);
                    foreach ($lignepages as $ligne){
                        $volumebrh = $volumebrh +  $ligne->getCubageLignepagebrh();
                    }
                    if ($volumebrh > 0){
                        $nb_pages = $nb_pages + 1;
                    }
                }
            }
            return $nb_pages;
        }

    function getNbArbres(Documentbrh $documentbrh):int
    {
        $nbarbres = 0;
        if($documentbrh){
            $pagebrh =$this->m->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$documentbrh]);
            foreach ($pagebrh as $page){
                $lignepages = $this->m->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$page]);
                foreach ($lignepages as $ligne){
                    if ($ligne->getLettreLignepagebrh() == "A"){
                        $nbarbres = $nbarbres + 1;
                    }
                }
            }
        }
            return $nbarbres;
    }

    function getNbBilles(Documentbrh $documentbrh):int
    {
        $nbbilles = 0;
        if($documentbrh){
            $pagebrh =$this->m->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$documentbrh]);
            foreach ($pagebrh as $page){
                $lignepages = $this->m->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$page]);
                foreach ($lignepages as $ligne){
                    $nbbilles = $nbbilles + 1;
                }
            }
        }
        return $nbbilles;
    }


    #[Route('/snvlt/docbrh/op/pages_brh/data/add_lignes/{data}/{id_foret}', name: 'adddata_brh_json')]
    public function add_lignes_brh(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        string $data,
        int $id_foret,
        NotificationRepository $notification,
        PagebrhRepository $pages_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT')  or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                //$page_brh = $pages_brh->find($id_page);
                if($data){
                    $lignebrh = new Lignepagebrh();


                    //Decoder le JSON BCBGFH
                    $arraydata = json_decode($data);
                    $isSameValue = false;

                    //Recherche la foret
                    $mes_attributions = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$registry->getRepository(Foret::class)->find($id_foret)]);
                    foreach($mes_attributions as $attribution){
                        $mes_reprises =$registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution]);
                        foreach($mes_reprises as $reprise){
                            $mes_brh = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$reprise]);
                            foreach($mes_brh as $brh){
                                $mes_pages = $registry->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$brh]);
                                foreach($mes_pages as $pagebrh){
                                    $mes_lignes = $registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$pagebrh]);
                                    foreach($mes_lignes as $ligne){
                                        if ($ligne->getNumeroLignepagebrh() == (int) $arraydata->numero_lignepagebrh &&
                                            $ligne->getLettreLignepagebrh() == $arraydata->lettre_lignepagebrh
                                        ){
                                            $isSameValue = true;
                                        }
                                    }
                                }
                            }
                        }
                    }
                   // dd((int) $arraydata->numero_lignepagebrh . " - " . $arraydata->lettre_lignepagebrh);
                    if($isSameValue == false){

                        //dd($arraydata->numero_lignepagebrh);
                        $date_jour = new \DateTime();
                        $arbre = $registry->getRepository(Lignepagecp::class)->find((int) $arraydata->numero_lignepagebrh);
                        if ($arbre and $this->isGranted('ROLE_EXPLOITANT')){
							$lignebrh->setNumeroLignepagebrh((int) $arbre->getNumeroArbrecp());
							$lignebrh->setNomEssencebrh($arbre->getNomEssencecp());
							$lignebrh->setZhLignepagebrh($arbre->getZhArbrecp());
							$arbre->setCharge(true);
						} else {
							$lignebrh->setNumeroLignepagebrh((int) $arraydata->numero_lignepagebrh);
							$essence = $registry->getRepository(Essence::class)->find((int) $arraydata->nom_essencebrh);
							if($essence){
							$lignebrh->setNomEssencebrh($essence);
							}
							$zh = $registry->getRepository(ZoneHemispherique::class)->find((int) $arraydata->zh_lignepagebrh);
							if($zh){
							$lignebrh->setZhLignepagebrh($zh);
							}
						}

                        $lignebrh->setLettreLignepagebrh($arraydata->lettre_lignepagebrh);
                        $lignebrh->setXLignepagebrh((float) $arraydata->x_lignepagebrh);
                        $lignebrh->setYLignepagebrh((float)$arraydata->y_lignepagebrh);
                        $lignebrh->setLongeurLignepagebrh((int) $arraydata->longeur_lignepagebrh);
                        $lignebrh->setDiametreLignepagebrh((int) $arraydata->diametre_lignepagebrh);
                        $lignebrh->setCubageLignepagebrh((float)$arraydata->cubage_lignepagebrh);
                        $lignebrh->setCreatedAt($date_jour);
                        $lignebrh->setCreatedBy($user);
                        $lignebrh->setCodePagebrh($registry->getRepository(Pagebrh::class)->find((int) $arraydata->code_pagebrh));
                        $lignebrh->setCodeLigneCp($arbre);
						$lignebrh->setExercice($this->administrationService->getAnnee());
                        $registry->getManager()->persist($lignebrh);


                        //Mise à jour de l'arbre et la bille CP
						if ($arbre){
							if ($lignebrh->getLettreLignepagebrh() == "A"){
								$arbre->setAUtlise(true);
								}elseif ($lignebrh->getLettreLignepagebrh() == "B"){
										$arbre->setBUtilise(true);
								}elseif ($lignebrh->getLettreLignepagebrh() == "C"){
										$arbre->setCUtilise(true);
								}
                        $registry->getManager()->persist($arbre);
						}


                        $this->administrationService->save_action(
                            $user,
                            'LIGNE_PAGE_BCBGFH',
                            'AJOUT',
                            new \DateTimeImmutable(),
                            'La bille N° ' . $lignebrh->getNumeroLignepagebrh(). $lignebrh->getLettreLignepagebrh() . " a été enregistrée par ". $user
                        );

                        $registry->getManager()->flush();

                        // Mise à jour des données dans SNVLT1

                        try {
                            $this->snvlt1->AddLignePageDocbrh($lignebrh);
                        }catch (\Throwable $exception){
                            $this->addFlash("Error","Erreur de Mise à Jour LignePagebrh depuis SNVLT TAMPON");
                        }
                        $response = array();
                        $response[] = array(
                            'code_brh'=>Lignepagebrh::LIGNE_BCBGFH_ADDED_SUCCESSFULLY,
                            'html'=>''
                        );

                    } else {
                        $response[] = array(
                            'code_brh'=>'SAME_NUMBER',
                            'html'=>''
                    );

                    }
                    return  new JsonResponse(json_encode($response));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/docbrh/op/pages_brh/data/add_lignes/db/{data}/{id_foret}', name: 'adddata_brh_db_json')]
    public function add_lignes_brh_db(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        string $data,
        int $id_foret,
        NotificationRepository $notification,
        PagebrhRepository $pages_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT')  or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                //$page_brh = $pages_brh->find($id_page);
                if($data){
                    $lignebrh = new Lignepagebrh();


                    //Decoder le JSON BCBGFH
                    $arraydata = json_decode($data);

                        $date_jour = new \DateTime();
                        $arbre = $registry->getRepository(Lignepagecp::class)->find((int) $arraydata->numero_lignepagebrh);
                        if ($arbre and $this->isGranted('ROLE_EXPLOITANT')){
							$lignebrh->setNumeroLignepagebrh((int) $arbre->getNumeroArbrecp());
							$lignebrh->setNomEssencebrh($arbre->getNomEssencecp());
							$lignebrh->setZhLignepagebrh($arbre->getZhArbrecp());
							$arbre->setCharge(true);
						} else {
							$lignebrh->setNumeroLignepagebrh((int) $arraydata->numero_lignepagebrh);
							$essence = $registry->getRepository(Essence::class)->find((int) $arraydata->nom_essencebrh);
							if($essence){
							$lignebrh->setNomEssencebrh($essence);
							}
							$zh = $registry->getRepository(ZoneHemispherique::class)->find((int) $arraydata->zh_lignepagebrh);
							if($zh){
							$lignebrh->setZhLignepagebrh($zh);
							}
						}
                    try {
                        $lignebrh->setLettreLignepagebrh($arraydata->lettre_lignepagebrh);
                        $lignebrh->setXLignepagebrh((float) $arraydata->x_lignepagebrh);
                        $lignebrh->setYLignepagebrh((float)$arraydata->y_lignepagebrh);
                        $lignebrh->setLongeurLignepagebrh((int) $arraydata->longeur_lignepagebrh);
                        $lignebrh->setDiametreLignepagebrh((int) $arraydata->diametre_lignepagebrh);
                        $lignebrh->setCubageLignepagebrh((float)$arraydata->cubage_lignepagebrh);
                        $lignebrh->setCreatedAt($date_jour);
                        $lignebrh->setCreatedBy($user);
                        $lignebrh->setCodePagebrh($registry->getRepository(Pagebrh::class)->find((int) $arraydata->code_pagebrh));
                        $lignebrh->setCodeLigneCp($arbre);
                        $lignebrh->setExercice($this->administrationService->getAnnee());
                        $registry->getManager()->persist($lignebrh);


                        //Mise à jour de l'arbre et la bille CP
                        if ($arbre){
                            if ($lignebrh->getLettreLignepagebrh() == "A"){
                                $arbre->setAUtlise(true);
                            }elseif ($lignebrh->getLettreLignepagebrh() == "B"){
                                $arbre->setBUtilise(true);
                            }elseif ($lignebrh->getLettreLignepagebrh() == "C"){
                                $arbre->setCUtilise(true);
                            }
                            $registry->getManager()->persist($arbre);
                        }


                        $this->administrationService->save_action(
                            $user,
                            'LIGNE_PAGE_BCBGFH',
                            'AJOUT',
                            new \DateTimeImmutable(),
                            'La bille N° ' . $lignebrh->getNumeroLignepagebrh(). $lignebrh->getLettreLignepagebrh() . " a été enregistrée par ". $user
                        );

                        $registry->getManager()->flush();

                        // Mise à jour des données dans SNVLT1
                        try {
                            $this->snvlt1->AddLignePageDocbrh($lignebrh);
                        }catch (\Throwable $exception){
                            $this->addFlash("Error","Erreur de Mise à Jour LignePagebrh depuis SNVLT TAMPON");
                        }
                        $response = array();
                        $response[] = array(
                            'code_brh'=>Lignepagebrh::LIGNE_BCBGFH_ADDED_SUCCESSFULLY,
                            'html'=>''
                        );
                    }catch (Exception $exception){
                        $response[] = array(
                            'code_brh'=>$exception->getMessage()
                        );
                    }



                    return  new JsonResponse(json_encode($response));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
    #[Route('snvlt/docbrh/op/pages_brh/data/verif/{numero_bille}/{id_foret}', name: 'search_ligne_brh')]
    public function search_ligne_brh(
        Request $request,
        UserRepository $userRepository,
        string $numero_bille,
        int $id_foret,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT')  or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE'))
            {
                if($numero_bille and $id_foret){

                    $isSameValue = false;
                    $response = array();
                    //Recherche la foret

                    $mes_attributions = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$registry->getRepository(Foret::class)->find($id_foret)]);
                    foreach($mes_attributions as $attribution){
                        $mes_reprises =$registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution]);
                        foreach($mes_reprises as $reprise){
                            $mes_brh = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$reprise]);
                            foreach($mes_brh as $brh){
                                $mes_pages = $registry->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$brh]);
                                foreach($mes_pages as $pagebrh){
                                    $mes_lignes = $registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$pagebrh]);
                                    foreach($mes_lignes as $ligne){
                                        $numero = $ligne->getNumeroLignepagebrh().$ligne->getLettreLignepagebrh();
                                        //dd($numero);
                                        if ($numero == $numero_bille){
                                            $isSameValue = true;
                                        }
                                    }
                                }
                            }
                        }
                    }
                   // dd((int) $arraydata->numero_lignepagebrh . " - " . $arraydata->lettre_lignepagebrh);
                    if($isSameValue == true){
                        $response[] = array(
                            'reponse'=>'TRUE'
                        );
                    } else {
                        $response[] = array(
                            'reponse'=>'FALSE'
                        );
                    }
                    return  new JsonResponse(json_encode($response));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/docbrh/op/pages_brh/data/edit/{id_page}/{data}', name: 'edit_page_brh_json')]
    public function edit_page_brh(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        string $data,
        int $id_page,
        NotificationRepository $notification,
        PagebrhRepository $pages_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                //$page_brh = $pages_brh->find($id_page);
                if($data){
                    $pagebrh = $registry->getRepository(Pagebrh::class)->find($id_page);

                    if ($pagebrh){
                        //Decoder le JSON BCBGFH
                        $arraydata = json_decode($data);

                        //dd($arraydata->numero_lignepagebrh);
                        $date_jour = new \DateTime();
                        $ville = $registry->getRepository(SousPrefecture::class)->find((int) $arraydata->destination_pagebrh);
                        if ($ville){
                            $pagebrh->setDestinationPagebrh(strtoupper($ville->getNomSousprefecture()));
                        }

                        $pagebrh->setParcUsineBrh($registry->getRepository(Usine::class)->find((int)  $arraydata->parc_usine_brh));


                        $date_chargement = new \DateTime();
                        $date_chargement =  $arraydata->date_chargementbrh;



                        $pagebrh->setImmatcamion(strtoupper($arraydata->immatcamion));
                        $pagebrh->setCoutTransportbrh((int) $arraydata->cout_transportbrh);
                        $pagebrh->setVillagePagebrh(strtoupper($arraydata->village_pagebrh));
                        $pagebrh->setChauffeurbrh(strtoupper($arraydata->chauffeurbrh));
                        $pagebrh->setDateChargementbrh(\DateTime::createFromFormat('Y-m-d', $date_chargement));
                        $pagebrh->setUpdatedAt(new \DateTime());
                        $pagebrh->setUpdatedBy($user);

                        $pagebrh->setCodeDocbrh($registry->getRepository(Documentbrh::class)->find((int) $arraydata->code_docbrh));

                        if($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE')){
                            $pagebrh->setConfirmationUsine(true);
                            $pagebrh->setFini(true);
                            $pagebrh->setEntreLje(false);
                        }
                        $registry->getManager()->persist($pagebrh);
                        $registry->getManager()->flush();

                        // Enregistrement dans SNVLT1

                        try {
                            $this->snvlt1->EditPageDocbrh($pagebrh);
                        }catch (\Throwable $exception){
                            $this->addFlash("Error","Erreur de Mise à Jour Pagebrh depuis SNVLT TAMPON");
                        }
                        return  new JsonResponse([
                            'code'=>Pagebrh::PAGE_BRH_EDITED_SUCCESSFULLY,
                            'html'=>''
                        ]);
                    }

                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/detail_brh_loading/details/{id_page}', name:'app_my_loadings')]
    public function details_chargement(
        ManagerRegistry $registry,
        Request $request,
        int $id_page,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notifications
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or
                $this->isGranted('ROLE_ADMIN') or
                $this->isGranted('ROLE_ADMINISTRATIF' ) or
                $this->isGranted('ROLE_EXPLOITANT') or
                $this->isGranted('ROLE_INDUSTRIEL' ) ){


                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();


                    //dd($notification->getRelatedToId());
                    $pagebrh = $registry->getRepository(Pagebrh::class)->find($id_page);
                    if ($pagebrh) {


                            return $this->render('doc_stats/entetes/documentbrh/details_chargement.html.twig',
                                [
                                    'liste_menus'=>$menus->findOnlyParent(),
                                    "all_menus"=>$menus->findAll(),
                                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                                    'mes_notifs'=>$notifications->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                                    'groupe'=>$code_groupe,
                                    'chargement'=>$pagebrh,
                                    'liste_parent'=>$permissions
                                ]);

                    } else {
                        return new JsonResponse(json_encode(false));
                    }


            }else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/detail_brh_loading/accept/{id_page?0}', name:'accept_chargement')]
    public function accepter_chargement(
        ManagerRegistry $registry,
        Request $request,
        int $id_page,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notifications
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ( $this->isGranted('ROLE_INDUSTRIEL' ) ){



                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $pagebrh = $registry->getRepository(Pagebrh::class)->find($id_page);
                if ($pagebrh) {

                    $pagebrh->setConfirmationUsine(true);
                    $registry->getManager()->persist($pagebrh);
                    $registry->getManager()->flush();

                    // Mise à jour dans SNVLT1
                    try {
                        $this->snvlt1->EditPageDocbrh($pagebrh);
                    }catch (\Throwable $exception){
                        $this->addFlash("Error","Erreur de Mise à Jour Pagebrh depuis SNVLT TAMPON");
                    }

                    //Save Log

                    $this->administrationService->save_action(
                        $user,
                        "PAGE_BCBGFH",
                        "ACCEPTAION_CHARGEMENT_USINE",
                        new \DateTimeImmutable(),
                        "Chargement ". $pagebrh->getCodeDocbrh()->getTypeDocument()->getDenomination() . " N° " . $pagebrh->getCodeDocbrh()->getNumeroDocbrh() . " - Feuillet N° ". $pagebrh->getNumeroPagebrh() . " en provenance de " . $pagebrh->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getRaisonSocialeExploitant() . " [Forêt " . $pagebrh->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination() . "] a été accepté par l'usine " . $user->getCodeindustriel()->getRaisonSocialeUsine() . " {User : " . $user . "}"
                    );

                    //envoi Notification à l'exploitant
                    if ($pagebrh->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getEmailPersonneRessource()){
                        $this->utils->envoiNotification(
                            $registry,
                            "Infos sur le Chargement N° " . $pagebrh->getNumeroPagebrh() . " [" . $pagebrh->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination() . "]",
                            "Chargement ". $pagebrh->getCodeDocbrh()->getTypeDocument()->getDenomination() . " N° " . $pagebrh->getCodeDocbrh()->getNumeroDocbrh() . " - Feuillet N° ". $pagebrh->getNumeroPagebrh() . " a été accepté par l'usine  " . $pagebrh->getParcUsineBrh()->getRaisonSocialeUsine(),
                            $registry->getRepository(User::class)->findOneBy(['email'=>$pagebrh->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getEmailPersonneRessource()]),
                            $user,
                            "app_my_loadings_notifs",
                            "PAGE_BCBGFH",
                            $pagebrh->getId()
                        );
                    }


                    //envoi Notification à la DR de l'usine
                        $this->utils->envoiNotification(
                            $registry,
                            "Infos sur le Chargement N° " . $pagebrh->getNumeroPagebrh() . " [" . $pagebrh->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination() . "]",
                            "Chargement ". $pagebrh->getCodeDocbrh()->getTypeDocument()->getDenomination() . " N° " . $pagebrh->getCodeDocbrh()->getNumeroDocbrh() . " - Feuillet N° ". $pagebrh->getNumeroPagebrh() . " a été accepté par l'usine  " . $pagebrh->getParcUsineBrh()->getRaisonSocialeUsine(),
                            $registry->getRepository(User::class)->findOneBy(['email'=>$pagebrh->getParcUsineBrh()->getCodeCantonnement()->getCodeDr()->getEmailPersonneRessource()]),
                            $user,
                            "app_my_loadings_notifs",
                            "PAGE_BCBGFH",
                            $pagebrh->getId()
                        );

                    //envoi Notification à la DD de l'usine si elle existe
                    if ($user->getCodeindustriel()->getCodeCantonnement()->getCodeDdef()->getId() > 0){
                    if ($user->getCodeindustriel()->getCodeCantonnement()->getCodeDdef()->getEmailPersonneRessource()){
                       // dd($user->getCodeindustriel()->getCodeCantonnement()->getCodeDdef()->getId());
                        $this->utils->envoiNotification(
                            $registry,
                            "Infos sur le Chargement N° " . $pagebrh->getNumeroPagebrh() . " [" . $pagebrh->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination() . "]",
                            "Chargement ". $pagebrh->getCodeDocbrh()->getTypeDocument()->getDenomination() . " N° " . $pagebrh->getCodeDocbrh()->getNumeroDocbrh() . " - Feuillet N° ". $pagebrh->getNumeroPagebrh() . " a été accepté par l'usine  " . $pagebrh->getParcUsineBrh()->getRaisonSocialeUsine(),
                            $registry->getRepository(User::class)->findOneBy(['email'=>$pagebrh->getParcUsineBrh()->getCodeCantonnement()->getCodeDdef()->getEmailPersonneRessource()]),
                            $user,
                            "app_my_loadings_notifs",
                            "PAGE_BCBGFH",
                            $pagebrh->getId()
                        );
                      }
                    }

                    //envoi Notification au cantonnement de l'usine
                    $this->utils->envoiNotification(
                        $registry,
                        "Infos sur le Chargement N° " . $pagebrh->getNumeroPagebrh() . " [" . $pagebrh->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination() . "]",
                        "Chargement ". $pagebrh->getCodeDocbrh()->getTypeDocument()->getDenomination() . " N° " . $pagebrh->getCodeDocbrh()->getNumeroDocbrh() . " - Feuillet N° ". $pagebrh->getNumeroPagebrh() . " a été accepté par l'usine  " . $pagebrh->getParcUsineBrh()->getRaisonSocialeUsine(),
                        $registry->getRepository(User::class)->findOneBy(['email'=>$pagebrh->getParcUsineBrh()->getCodeCantonnement()->getEmailPersonneRessource()]),
                        $user,
                        "app_my_loadings_notifs",
                        "PAGE_BCBGFH",
                        $pagebrh->getId()
                    );

                    return new JsonResponse(json_encode(Pagebrh::PAGE_BRH_ACCEPTED));


                } else {
                    return new JsonResponse(json_encode("BAD NOTIFICATION"));
                }

            }else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/detail_brh_loading/{id_notification?0}', name:'app_my_loadings_notifs')]
    public function details_chargements_notifs(
        ManagerRegistry $registry,
        Request $request,
        int $id_notification,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notifications
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or
                $this->isGranted('ROLE_ADMIN') or
                $this->isGranted('ROLE_ADMINISTRATIF' ) or
                $this->isGranted('ROLE_EXPLOITANT') or
                $this->isGranted('ROLE_INDUSTRIEL' ) ){

                $notification = $notifications->find($id_notification);

                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                if ($notification) {


                    //dd($notification->getRelatedToId());
                    $pagebrh = $registry->getRepository(Pagebrh::class)->find($notification->getRelatedToId());
                    if ($pagebrh) {


                        return $this->render('doc_stats/entetes/documentbrh/details_chargement.html.twig',
                            [
                                'liste_menus'=>$menus->findOnlyParent(),
                                "all_menus"=>$menus->findAll(),
                                'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                                'mes_notifs'=>$notifications->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                                'groupe'=>$code_groupe,
                                'chargement'=>$pagebrh,
                                'liste_parent'=>$permissions
                            ]);

                    } else {
                        return new JsonResponse(json_encode(false));
                    }


                } else {
                    return new JsonResponse(json_encode("BAD NOTIFICATION"));
                }

            }else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/usines_cantonnement/l/{id_ville}', name:'usines_cantonnement')]
    public function usines_cantonnement(
        ManagerRegistry $registry,
        Request $request,
        int $id_ville,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notifications
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or
                $this->isGranted('ROLE_ADMIN') or
				$this->isGranted('ROLE_DPIF_SAISIE') or
                $this->isGranted('ROLE_ADMINISTRATIF' ) or
                $this->isGranted('ROLE_EXPLOITANT') or
                $this->isGranted('ROLE_INDUSTRIEL' ) ){

                $liste_usines = array();
                $cantonnement = $registry->getRepository(Cantonnement::class)->find($id_ville);
                if($this->isGranted('ROLE_DPIF_SAISIE')){
					$usines = $registry->getRepository(Usine::class)->findBy([],['raison_sociale_usine'=>'ASC']);
                    foreach ($usines as $usine){
                        $liste_usines[] = array(
                            'id_usine'=>$usine->getId(),
                            'rs'=>$usine->getRaisonSocialeUsine()
                        );
                    }
                    return new JsonResponse(json_encode($liste_usines));
				}
				elseif ($cantonnement){
                    $usines = $registry->getRepository(Usine::class)->findBy(['code_cantonnement'=>$cantonnement],['raison_sociale_usine'=>'ASC']);
                    foreach ($usines as $usine){
                        $liste_usines[] = array(
                            'id_usine'=>$usine->getId(),
                            'rs'=>$usine->getRaisonSocialeUsine()
                        );
                    }
                    return new JsonResponse(json_encode($liste_usines));

                    }
                    else {
                            return new JsonResponse(json_encode(false));
                        }


                } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/numcp/dispo/{id_page}', name:'num_cp_dispo')]
    public function num_cp_dispo(
        ManagerRegistry $registry,
        Request $request,
        int $id_page,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notifications
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE')){

                $liste_numeros = array();

                $reprise = $registry->getRepository(Pagebrh::class)->find($id_page)->getCodeDocbrh()->getCodeReprise();
//dd($reprise);
                if ($reprise){
                    $doccp = $registry->getRepository(Documentcp::class)->findBy(['code_reprise'=>$reprise]);
                    foreach ($doccp as $doc){
                        $pagecps = $registry->getRepository(Pagecp::class)->findBy(['code_doccp'=>$doc]);
                        //dd($pagecps);
                        foreach ($pagecps as $pagecp){
                            $lignes = $registry->getRepository(Lignepagecp::class)->findBy(['code_pagecp'=>$pagecp, 'fut_abandon'=>false]);

                            foreach ($lignes as $ligne){
                                if (!$ligne->isFutAbandon()){
                                    if ($ligne->isAUtlise() == false or  $ligne->isBUtilise() == false or  $ligne->isCUtilise() == false){
                                        $liste_numeros[] = array(
                                            'id_ligne'=>$ligne->getId(),
                                            'num'=>$ligne->getNumeroArbrecp()
                                        );
                                    }
                                }
                            }
                        }

                    }
                    return new JsonResponse(json_encode($liste_numeros));

                }
                else {
                    return new JsonResponse(json_encode(false));
                }


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/numcp/arbre_infos/{id_bille}', name:'info_bille_cp')]
    public function info_bille_cp(
        ManagerRegistry $registry,
        Request $request,
        int $id_bille,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notifications
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE') or $this->isGranted('ROLE_ADMINISTRATIF')){

                $infos_bille = array();

                $bille = $registry->getRepository(Lignepagecp::class)->find($id_bille);

                if ($bille){
                    $infos_bille[] = array(
                            'id_ligne'=>$bille->getId(),
                            'num'=>$bille->getNumeroArbrecp(),
                            'essence'=>$bille->getNomEssencecp()->getNomVernaculaire(),
                            'zh'=>$bille->getZhArbrecp()->getZone(),
                            'x_arbre'=>$bille->getXArbrecp(),
                            'y_arbre'=>$bille->getYArbrecp()
                        );
                    return new JsonResponse(json_encode($infos_bille));

                }
                else {
                    return new JsonResponse(json_encode(false));
                }


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/numcp/dispo/lettres/{id_bille}', name:'lettres_bille_cp')]
    public function lettres_bille_cp(
        ManagerRegistry $registry,
        Request $request,
        int $id_bille,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notifications
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE') or $this->isGranted('ROLE_ADMINISTRATIF')){

                $infos_bille = array();

                $bille = $registry->getRepository(Lignepagecp::class)->find($id_bille);

                if ($bille){
                    if ($bille->getLongeuraBillecp() && !$bille->isAUtlise() && !$bille->isAAbandon()){
                        $infos_bille[] = array(
                            'lettre'=>'A'
                        );
                    }
                    if ($bille->getLongeurbBillecp() && !$bille->isBUtilise() && !$bille->isBAbandon()){
                        $infos_bille[] = array(
                            'lettre'=>'B'
                        );
                    }
                    if ($bille->getLongeurcBillecp() && !$bille->isCUtilise() && !$bille->isCAbandon()){
                        $infos_bille[] = array(
                            'lettre'=>'C'
                        );
                    }

                    return new JsonResponse(json_encode($infos_bille));

                }
                else {
                    return new JsonResponse(json_encode(false));
                }


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/numcp/billes/donnees/{id_bille}/{lettre}', name:'mensurations_bille_cp')]
    public function mensurations_bille_cp(
        ManagerRegistry $registry,
        Request $request,
        int $id_bille,
        string $lettre,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notifications
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE')  ){

                $infos_bille = array();

                $bille = $registry->getRepository(Lignepagecp::class)->find($id_bille);

                if ($bille){

                    if ($lettre == "A" && !$bille->isAUtlise()){
                        $infos_bille[] = array(
                            'lng'=>$bille->getLongeuraBillecp(),
                            'dm'=>$bille->getDiametreaBillecp(),
                            'vol'=>$bille->getVolumeaBillecp()
                        );
                    } elseif($lettre == "B" && !$bille->isBUtilise()){
                        $infos_bille[] = array(
                            'lng'=>$bille->getLongeurbBillecp(),
                            'dm'=>$bille->getDiametrebBillecp(),
                            'vol'=>$bille->getVolumebBillecp()
                        );
                    }elseif($lettre == "C" && !$bille->isCUtilise()){
                        $infos_bille[] = array(
                            'lng'=>$bille->getLongeurcBillecp(),
                            'dm'=>$bille->getDiametrecBillecp(),
                            'vol'=>$bille->getVolumecBillecp()
                        );
                    }

                    return new JsonResponse(json_encode($infos_bille));

                }
                else {
                    return new JsonResponse(json_encode(false));
                }


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/docbrh/validate_chargement/page/{id_page}', name: 'affiche_page_courante_validation')]
    public function affiche_page_courante_validation(
        Request $request,
        int $id_page,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        PagebrhRepository $pages_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $page_brh = $pages_brh->find($id_page);
                $my_brh_page = array();
                if($page_brh){
                    $parc_usine = "";
                    if($page_brh->getDateChargementbrh()) { $date_chargement = $page_brh->getDateChargementbrh()->format('d/m/Y');} else { $date_chargement = ""; }
                    //dd($page_brh->getDateChargementbrh()->format('d/m/Y'));
                    if($page_brh->getParcUsineBrh()) {
                        if($page_brh->getParcUsineBrh()->getSigle()) {
                            $parc_usine = $page_brh->getParcUsineBrh()->getSigle();
                        } else {
                            $parc_usine = $page_brh->getParcUsineBrh()->getRaisonSocialeUsine();
                        }
                    }


                    $lignes_brh = $registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$page_brh]);
                    if ($page_brh->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getSigle()){
                        $exploitant = $page_brh->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getSigle();
                    } else {
                        $exploitant = $page_brh->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getRaisonSocialeExploitant();
                    }

                    if ($page_brh->getParcUsineBrh()){
                        if ($page_brh->getParcUsineBrh()->getSigle()){
                            $usine = $page_brh->getParcUsineBrh()->getSigle();
                        } else {
                            $usine = $page_brh->getParcUsineBrh()->getRaisonSocialeUsine();
                        }
                    }





                    foreach($lignes_brh as $ligne){

						if ($ligne->getNomEssencebrh()){

                            $essence = $ligne->getNomEssencebrh()->getNomVernaculaire();
							} else {
								$essence = "-";
							}

						if ($ligne->getZhLignepagebrh()){

								$zone = $ligne->getZhLignepagebrh()->getZone();
							} else {
								$zone = "-";
							}

                        $my_brh_page[] = array(
                            'id_page'=>$page_brh->getId(),
                            'foret'=>$page_brh->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination(),
                            'numero_page'=>$page_brh->getNumeroPagebrh(),
                            'numero_doc'=>$page_brh->getCodeDocbrh()->getNumeroDocbrh(),
                            'date_chargement'=>$date_chargement,
                            'destination'=>$parc_usine. " - [".$page_brh->getDestinationPagebrh()."]" ,
                            'id_arbre'=>$ligne->getId(),
                            'numero_arbre'=>$ligne->getNumeroLignepagebrh(),
                            'lettre_arbre'=>$ligne->getLettreLignepagebrh(),
                            'essence'=>$essence,
                            'zone'=>$zone,
                            'x'=>$ligne->getXLignepagebrh(),
                            'y'=>$ligne->getYLignepagebrh(),
                            'lng'=>$ligne->getLongeurLignepagebrh(),
                            'dm'=>$ligne->getDiametreLignepagebrh(),
                            'vol'=>$ligne->getCubageLignepagebrh(),
                            'photo'=>$page_brh->getPhoto(),
                            'exploitant'=>$exploitant,
                            'parc_usine'=>$usine
                        );
                    }


                    return  new JsonResponse(json_encode($my_brh_page));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    /*Photo du feuillet*/
    #[Route('/snvlt/docbrh/op/pages/p/{id_page?0}', name: 'photo_page_brh')]
    public function photo_page_brh(
        Request $request,
        int $id_page,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE') or $this->isGranted('ROLE_ADMINISTRATIF'))
                {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $numerodoc = "";

                $pagebrh = $registry->getRepository(Pagebrh::class)->find($id_page);

                    $form = $this->createForm(PagebrhType::class, $pagebrh);

                    $form->handleRequest($request);

                    if ($form->isSubmitted() && $form->isValid()) {

                        if($pagebrh){
                        $fichier = $form->get('photo')->getData();

                        if ($fichier) {$originalFilename = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);


                            $safeFilename = $this->slugger->slug($originalFilename);
                            $newFilename = $this->utils->uniqidReal(30).'.'.$fichier->guessExtension();

                            // Move the file to the directory where brochures are stored
                            try {
                                $fichier->move(
                                    $this->getParameter('images_brh_directory'),
                                    $newFilename
                                );
                                //sleep(4);
                            } catch (FileException $e) {
                                // ... handle exception if something happens during file upload
                            }

                            // updates the 'brochureFilename' property to store the PDF file name
                            // instead of its contents
                            $pagebrh->setPhoto($newFilename);

                        }

                        //dd($this->getParameter('prospections_csv_directory'),);

                        $pagebrh->setUpdatedAt(new \DateTime());
                        $pagebrh->setUpdatedBy($user);


                        $manager = $registry->getManager();
                        $manager->persist($pagebrh);
                        $manager->flush();


                        return $this->redirectToRoute("affichage_brh_json", [
                            'id_brh' => $pagebrh->getCodeDocbrh()->getId()
                        ]);

                    }
                } else {
                        return $this->render('doc_stats/entetes/documentbrh/photo_brh.html.twig', [

                            'liste_menus'=>$menus->findOnlyParent(),
                            "all_menus"=>$menus->findAll(),
                            'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                            'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                            'groupe'=>$code_groupe,
                            'liste_parent'=>$permissions,
                            'form'=>$form->createView(),
                            'feuillet'=>$pagebrh
                        ]);
                    }


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    /*Photo du feuillet*/
    #[Route('/snvlt/docbrh/op/f/p/{id_page?0}', name: 'photo_feuillet_brh')]
    public function photo_feuillet_brh(
        Request $request,
        int $id_page,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE') or $this->isGranted('ROLE_ADMINISTRATIF'))
                {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $numerodoc = "";

                $pagebrh = $registry->getRepository(Pagebrh::class)->find($id_page);
                if($pagebrh){
                    return $this->render('doc_stats/entetes/documentbrh/feuillet_photo.html.twig', [

                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                        'groupe'=>$code_groupe,
                        'liste_parent'=>$permissions,
                        'feuillet'=>$pagebrh
                    ]);
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

	#[Route('snvlt/brh/edit/{id_bille}/B2', name:'edit_bille')]
    public function edit_bille(
        ManagerRegistry $registry,
        Request $request,
        int $id_bille,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notifications
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN')){

                $infos_bille = array();

                $bille = $registry->getRepository(Lignepagebrh::class)->find($id_bille);

                if ($bille){
                    $infos_bille[] = array(
                        'id_ligne'=>$bille->getId(),
                        'numero'=>$bille->getNumeroLignepagebrh(),
                        'essence'=>$bille->getNomEssencebrh()->getId(),
                        'zh'=>$bille->getZhLignepagebrh()->getId(),
                        'x'=>$bille->getXLignepagebrh(),
                        'y'=>$bille->getYLignepagebrh(),
                        'lettre'=>$bille->getLettreLignepagebrh(),
                        'lng'=>$bille->getLongeurLignepagebrh(),
                        'dm'=>$bille->getDiametreLignepagebrh(),
                        'cubage'=>$bille->getCubageLignepagebrh()
                    );
                    return new JsonResponse(json_encode($infos_bille));

                }
                else {
                    return new JsonResponse(json_encode(false));
                }


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

	#[Route('/snvlt/docbrh/op/data/edit_lignes/{data}/{id_bille}', name: 'editdata_brh_json')]
    public function edit_lignes_brh(
        Request $request,
        UserRepository $userRepository,
        User $user = null,
        string $data,
        int $id_bille,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $date_jour = new \DateTime();

                $response = array();

                $bille = $registry->getRepository(Lignepagebrh::class)->find($id_bille);
                if ($bille){
                    if($data){
                        $arraydata = json_decode($data);

                            $bille->setNumeroLignepagebrh((int)$arraydata->numero_lignepagebrh);
                            $bille->setLettreLignepagebrh($arraydata->lettre_lignepagebrh);
                            $bille->setXLignepagebrh((float) $arraydata->x_lignepagebrh);
                            $bille->setYLignepagebrh((float)$arraydata->y_lignepagebrh);
                            $bille->setLongeurLignepagebrh((int) $arraydata->longeur_lignepagebrh);
                            $bille->setDiametreLignepagebrh((int) $arraydata->diametre_lignepagebrh);
                            $bille->setCubageLignepagebrh((float)$arraydata->cubage_lignepagebrh);
                            $bille->setUpdatedAt($date_jour);
                            $bille->setUpdatedBy($user);

                            $registry->getManager()->persist($bille);
                            $registry->getManager()->flush();

                            // Mise à jour dans SNVLT1

                        try {
                            $this->snvlt1->EditLignePageDocbrh($bille);
                        }catch (\Throwable $exception){
                            $this->addFlash("Error","Erreur de Mise à Jour LignePagebrh depuis SNVLT TAMPON");
                        }

                        $this->administrationService->save_action(
                                $user,
                                'LIGNE_PAGE_BCBGFH',
                                'MODIFICATION',
                                new \DateTimeImmutable(),
                                'La bille N° ' . $bille->getNumeroLignepagebrh(). $bille->getLettreLignepagebrh() . " a été modifiée par ". $user
                            );


                            $response[] = array(
                                'code_brh'=>Lignepagebrh::LIGNE_BCBGFH_ADDED_SUCCESSFULLY
                            );

                        } else {
                                $response[] = array(
                                    'code_brh' => 'SAME_NUMBER'
                                );
                            }
                        return  new JsonResponse(json_encode($response));
                    }


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }




	#[Route('snvlt/brh/del/{id_bille}/B1', name:'del_bille')]
    public function del_bille(
        ManagerRegistry $registry,
        Request $request,
        int $id_bille
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN')){

                $infos_bille = array();

                $bille = $registry->getRepository(Lignepagebrh::class)->find($id_bille);

                if ($bille){
                    $registry->getManager()->remove($bille);
                    $registry->getManager()->flush();

                    // Mise à jour dans SNVLT1

                    try {
                        $this->snvlt1->DeleteLignePageDocbrh($bille);
                    }catch (\Throwable $exception){
                        $this->addFlash("Error","Erreur de Suppression LignePagebrh depuis SNVLT TAMPON");
                    }

                    $this->administrationService->save_action(
                        $this->getUser(),
                        'LIGNE_PAGE_BCBGFH',
                        'SUPPRESSION',
                        new \DateTimeImmutable(),
                        'La bille N° ' . $bille->getNumeroLignepagebrh(). $bille->getLettreLignepagebrh() . " a été supprimée par ". $this->getUser()
                    );

                    $infos_bille[] = array(
                        'valeur'=>"SUCCESS"
                    );
                    return new JsonResponse(json_encode($infos_bille));

                }
                else {
                    return new JsonResponse(json_encode(false));
                }


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/docbrh/op/pages_brh/change_num/{id_page}/{numero_feuillet}', name:'renomme_num_feuillet')]
    public function renomme_num_feuillet(
        ManagerRegistry $registry,
        Request $request,
        int $id_page,
        string $numero_feuillet,
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN')){

                $infos = array();

                $feuillet = $registry->getRepository(Pagebrh::class)->find($id_page);


                if ($feuillet && $numero_feuillet){
                        $ancien_numero =$feuillet->getNumeroPagebrh();
                        $feuillet->setNumeroPagebrh($numero_feuillet);

                    $registry->getManager()->persist($feuillet);
                    $registry->getManager()->flush();

                    // Mise à jour dans SNVLT1
                    try {
                        $this->snvlt1->EditPageDocbrh($feuillet);
                    }catch (\Throwable $exception){
                        $this->addFlash("Error","Erreur de Mise à jour Pagebrh depuis SNVLT TAMPON");
                    }


                    $this->administrationService->save_action(
                        $this->getUser(),
                        'PAGE BCBGFH',
                        'MODIFICATION',
                        new \DateTimeImmutable(),
                        'Le N° Feuillet ' . $ancien_numero." vient d'être remplacé par ". $feuillet->getNumeroPagebrh() . " [". $this->getUser(). "]"
                    );

                    $infos[] = array(
                        'valeur'=>"SUCCESS"
                    );


                } else {
                    $infos[] = array(
                        'valeur'=>"DENY"
                    );
                }

                return new JsonResponse(json_encode($infos));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/docbrh/op/pages_brh/ajout_num/{id_brh}/{numero_feuillet}', name:'ajout_num_feuillet')]
    public function ajout_num_feuillet(
        ManagerRegistry $registry,
        Request $request,
        int $id_brh,
        string $numero_feuillet,
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN')){

                $infos = array();

                $brh = $registry->getRepository(Documentbrh::class)->find($id_brh);


                if ($brh && $numero_feuillet){
                        $feuillet = new Pagebrh();
                        $feuillet->setNumeroPagebrh($numero_feuillet);
                        $i = 0;
                        foreach ($brh->getPagebrhs() as $pagebrh){
                            $i = $pagebrh->getindex_page();
                        }

                        $feuillet->setindex_page($i + 1);
                        $feuillet->setCodeDocbrh($brh);
                        $feuillet->setCreatedAt(new \DateTimeImmutable());
                        $feuillet->setCreatedBy($this->getUser());

                        $registry->getManager()->persist($feuillet);
                        $registry->getManager()->flush();

                        // Mise à jour dans SNVLT1
                    try {
                        $this->snvlt1->AddSinglePageDocbrh($feuillet);
                    }catch (\Throwable $exception){
                        $this->addFlash("Error","Erreur d'ajout de Pagebrh depuis SNVLT TAMPON");
                    }


                    $this->administrationService->save_action(
                        $this->getUser(),
                        'PAGE BCBGFH',
                        'MODIFICATION',
                        new \DateTimeImmutable(),
                        'Le N° Feuillet ' . $feuillet->getNumeroPagebrh()." vient d'être créé par ".  $this->getUser()
                    );

                    $infos[] = array(
                        'valeur'=>"SUCCESS"
                    );


                } else {
                    $infos[] = array(
                        'valeur'=>"DENY"
                    );
                }

                return new JsonResponse(json_encode($infos));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/docbrh/op/pages_brh/reset_num/{id_page}', name:'reset_num_feuillet')]
    public function reset_num_feuillet(
        ManagerRegistry $registry,
        Request $request,
        int $id_page
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN')){

                $infos = array();

                $feuillet = $registry->getRepository(Pagebrh::class)->find($id_page);


                if ($feuillet){
                    $value =new \DateTime("1900-01-01");
                    $feuillet->setCantonnementPagebrh(null);
                    $feuillet->setChauffeurbrh(null);
                    $feuillet->setConfirmationUsine(false);
                    $feuillet->setDateChargementbrh($value);
                    $feuillet->setCoutTransportbrh(0);
                    $feuillet->setImmatcamion(null);
                    $feuillet->setUpdatedAt(new \DateTime());
                    $feuillet->setChauffeurbrh(null);
                    $feuillet->setEntreLje(false);
                    $feuillet->setFini(false);
                    $feuillet->setMotivationRejet(null);
                    $feuillet->setPhoto(null);
                    $feuillet->setParcUsineBrh(null);
                    $feuillet->setDestinationPagebrh(null);
                    $feuillet->setSoumettre(false);
                    $feuillet->setSoumettre(false);
                    $feuillet->setVillagePagebrh(null);
                    $feuillet->setUpdatedBy($this->getUser());

                    $registry->getManager()->persist($feuillet);

                    // Suppression des lignes du feuillet
                    foreach ($feuillet->getLignepagebrhs() as $lignepagebrh){
                        $registry->getManager()->remove($lignepagebrh);

                        // Mise à jour dans SNVLT1
                        try {
                            $this->snvlt1->DeleteLignePageDocbrh($lignepagebrh);
                        }catch (\Throwable $exception){
                            $this->addFlash("Error","Erreur de suppression de lignePagebrh depuis SNVLT TAMPON");
                        }
                    }

                    $registry->getManager()->flush();


                    $this->administrationService->save_action(
                        $this->getUser(),
                        'PAGE BCBGFH',
                        'MODIFICATION',
                        new \DateTimeImmutable(),
                        'Le N° Feuillet ' . $feuillet->getNumeroPagebrh()." vient d'être réinitialisé par " .$this->getUser()
                    );

                    $infos[] = array(
                        'valeur'=>"SUCCESS"
                    );


                } else {
                    $infos[] = array(
                        'valeur'=>"DENY"
                    );
                }

                return new JsonResponse(json_encode($infos));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/docbrh/op/pages_brh/delete_num/{id_page}', name:'delete_num_feuillet')]
    public function delete_num_feuillet(
        ManagerRegistry $registry,
        Request $request,
        int $id_page
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN')){

                $infos = array();

                $feuillet = $registry->getRepository(Pagebrh::class)->find($id_page);


                if ($feuillet){
                    // Suppression des lignes du feuillet
                    foreach ($feuillet->getLignepagebrhs() as $lignepagebrh){
                        $registry->getManager()->remove($lignepagebrh);

                        // Mise à jour dans SNVLT1
                        try {
                            $this->snvlt1->DeleteLignePageDocbrh($lignepagebrh);
                        }catch (\Throwable $exception){
                            $this->addFlash("Error","Erreur de suppression de lignePagebrh depuis SNVLT TAMPON");
                        }
                    }

                    $registry->getManager()->remove($feuillet);

                    $registry->getManager()->flush();

                    // Mise à jour dans SNVLT1
                    try {
                        $this->snvlt1->DeletePageDocbrh($feuillet);
                    }catch (\Throwable $exception){
                        $this->addFlash("Error","Erreur de suppression de Pagebrh depuis SNVLT TAMPON");
                    }

                    $this->administrationService->save_action(
                        $this->getUser(),
                        'PAGE BCBGFH',
                        'SUPPRESSION',
                        new \DateTimeImmutable(),
                        'Le N° Feuillet ' . $feuillet->getNumeroPagebrh()." vient d'être supprimé par " .$this->getUser()
                    );

                    $infos[] = array(
                        'valeur'=>"SUCCESS"
                    );


                } else {
                    $infos[] = array(
                        'valeur'=>"DENY"
                    );
                }

                return new JsonResponse(json_encode($infos));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/docbrh/op/lignes_brh/delete_ligne/{id_ligne}', name:'delete_ligne_brh')]
    public function delete_ligne_brh(
        ManagerRegistry $registry,
        Request $request,
        int $id_ligne
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_EXPLOITANT')){

                $infos = array();

                $lignepagebrh = $registry->getRepository(Lignepagebrh::class)->find($id_ligne);
                //dd($lignepagebrh);
                    if ($lignepagebrh){

                        $registry->getManager()->remove($lignepagebrh);

                        //Recherche Arbre associé
                        if ($lignepagebrh->getCodeLigneCp()){
                            $arbre = $registry->getRepository(Lignepagecp::class)->find((int) $lignepagebrh->getCodeLigneCp()->getId());
                            //Mise à jour de l'arbre et la bille CP
                            if ($arbre){
                                if ($lignepagebrh->getLettreLignepagebrh() == "A"){
                                    $arbre->setAUtlise(false);
                                }elseif ($lignepagebrh->getLettreLignepagebrh() == "B"){
                                    $arbre->setBUtilise(false);
                                }elseif ($lignepagebrh->getLettreLignepagebrh() == "C"){
                                    $arbre->setCUtilise(false);
                                }

                                $registry->getManager()->persist($arbre);
                            }
                        }

                        $registry->getManager()->flush();

                        // Enregistrement dans SNVLT1
                        try {
                            $this->snvlt1->DeleteLignePageDocbrh($lignepagebrh);
                        }catch (\Throwable $exception){
                            $this->addFlash("Error","Erreur de suppression de lignePagebrh depuis SNVLT TAMPON");
                        }

                        $this->administrationService->save_action(
                            $this->getUser(),
                            'LIGNE BCBGFH',
                            'SUPPRESSION',
                            new \DateTimeImmutable(),
                            'La bille N° ' . $lignepagebrh->getNumeroLignepagebrh().$lignepagebrh->getLettreLignepagebrh()." vient d'être supprimée par " .$this->getUser()
                        );
                        $infos[] = array(
                            'valeur'=>"SUCCESS"
                        );

                    } else {
                        $infos[] = array(
                            'valeur'=>"DENY"
                        );
                    }


                return new JsonResponse(json_encode($infos));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/docbrh/op/lignes_brh/verifyingXY/{x}/{y}', name:'verifyingXY')]
    public function verifyingXY(
        ManagerRegistry $registry,
        Request $request,
        int $x,
        int $y
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_EXPLOITANT')){

                $infos = array();

                $lignepagebrh = $registry->getRepository(Lignepagebrh::class)->findOneBy(
                    [
                        'x_lignepagebrh'=>(float) $x,
                        'y_lignepagebrh'=>(float) $y
                    ]
                );
                $lignepagebcbp = $registry->getRepository(Lignepagebcbp::class)->findOneBy(
                    [
                        'x'=>(float) $x,
                        'y'=>(float) $y
                    ]
                );

                $lignepagebcbgfh = $registry->getRepository(Lignepagebcbgfh::class)->findOneBy(
                    [
                        'x_lignepagebcbgfh'=>(float) $x,
                        'y_lignepagebcbgfh'=>(float) $y
                    ]
                );
                if ($lignepagebrh or $lignepagebcbp or $lignepagebcbgfh){
                    if ($lignepagebrh){
                        $infos[] = array(
                            'valeur'=>"FOUND",
                            'description'=>"N° arbre : ". $lignepagebrh->getNumeroLignepagebrh() . " - BRH : ". $lignepagebrh->getCodePagebrh()->getCodeDocbrh()->getNumeroDocbrh()
                        );
                    }
                    if ($lignepagebcbp){
                        $infos[] = array(
                            'valeur'=>"FOUND",
                            'description'=>"N° arbre : ". $lignepagebcbp->getNumeroBille() . " - BRH : ". $lignepagebcbp->getCodePagebcbp()->getCodeDocbcbp()->getNumeroDocbcbp()
                        );
                    }
                    if ($lignepagebcbgfh){
                        $infos[] = array(
                            'valeur'=>"FOUND",
                            'description'=>"N° arbre : ". $lignepagebcbp->getNumeroBille() . " - BRH : ". $lignepagebcbp->getCodePagebcbp()->getCodeDocbcbp()->getNumeroDocbcbp()
                        );
                    }
                } else {
                    $infos[] = array(
                        'valeur'=>"NOT_FOUND",
                        'description'=>""
                    );
                }

                return new JsonResponse(json_encode($infos));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
}