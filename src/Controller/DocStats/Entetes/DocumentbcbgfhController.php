<?php

namespace App\Controller\DocStats\Entetes;

use App\Controller\Services\AdministrationService;
use App\Controller\Services\Utils;
use App\Entity\Admin\Exercice;
use App\Entity\Administration\FicheProspection;
use App\Entity\Administration\ProspectionTemp;
use App\Entity\Autorisation\Attribution;
use App\Entity\Autorisation\ContratBcbgfh;
use App\Entity\Autorisation\Reprise;
use App\Entity\DocStats\Entetes\Documentbcbgfh;
use App\Entity\DocStats\Entetes\Documentcp;
use App\Entity\DocStats\Pages\Pagebcbgfh;
use App\Entity\DocStats\Pages\Pagecp;
use App\Entity\DocStats\Saisie\Lignepagebcbgfh;
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
use App\Form\DocStats\Pages\PagebcbgfhType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\DocStats\Entetes\DocumentbcbgfhRepository;
use App\Repository\DocStats\Pages\PagebcbgfhRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class DocumentbcbgfhController extends AbstractController
{
    public function __construct(
        private ManagerRegistry $m,
        private Utils $utils,
        private AdministrationService $administrationService,
        private SluggerInterface $slugger)
    {
    }

    #[Route('/doc/stats/entetes/docbcbgfh', name: 'app_op_docbcbgfh')]
    public function index(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentbcbgfhRepository $docs_bcbgfh,
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

                return $this->render('doc_stats/entetes/documentbcbgfh/index.html.twig', [
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'groupe'=>$code_groupe,
                    'liste_parent'=>$permissions,
                    'usines_dest'=>$registry->getRepository(Usine::class)->findOnlyManager(),
                    'liste_forets'=>$registry->getRepository(Foret::class)->findBy(['code_type_foret'=>$registry->getRepository(TypeForet::class)->find(2)], ['denomination'=>'ASC']),
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/doc/stats/entetes/docbcbgfh/valid_respo', name: 'validation_respo')]
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

                return $this->render('doc_stats/entetes/documentbcbgfh/validation_respo.html.twig', [
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


    #[Route('/snvlt/bcbgfh/validation_chargement_respo', name: 'app_validation_chargements')]
    public function app_validation_chargements(
        Request $request,
        UserRepository $userRepository,
        PagebcbgfhRepository $page_bcbgfh,
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

                //Liste des pages ou chargements BCBGFH à valider
                $liste_chargements = array();

                $contrats = $registry->getRepository(ContratBcbgfh::class)->findBy(['code_exploitant'=>$user->getCodeexploitant()]);

                foreach($contrats as $contrat){

                        $documentsbcbgfhs = $registry->getRepository(Documentbcbgfh::class)->findBy(['code_contrat'=>$contrat]);
                        foreach ($documentsbcbgfhs as $documentbcbgfh){
                            $page_bcbgfh = $registry->getRepository(Pagebcbgfh::class)->findBy(['fini'=>false, 'soumettre'=>true, 'code_docbcbgfh'=>$documentbcbgfh]);
                            foreach($page_bcbgfh as $page){

                                $billes = $registry->getRepository(Lignepagebcbgfh::class)->findBy(['code_pagebcbgfh'=>$page]);
                                $nb_biolles = 0;
                                $volume = 0;

                                foreach($billes as $bille){
                                    $nb_biolles = $nb_biolles + 1;
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
                                    'numero_page'=>$page->getNumeroPagebcbgfh(),
                                    'numero_bcbgfh'=>$page->getCodeDocbcbgfh()->getNumeroDocbcbgfh(),
                                    'foret'=>$page->getCodeDocbcbgfh()->getCodeContrat()->getCodeForet()->getDenomination(),
                                    'destination_bcbgfh'=>$page->getDestinationPagebcbgfh(),
                                    'usine_dest'=>$usine,
                                    'date_chargement'=>$page->getDateChargementbcbgfh()->format('d m Y'),
                                    'nb_billes'=>$page->getNumeroPagebcbgfh(),
                                    'volume_bcbgfh'=>round($volume, 3),
                                    'immat'=>$page->getImmatcamion(),
                                    'conducteur'=>$page->getChauffeurbcbgfh()
                                );
                            }
                        }

                }





                return  new JsonResponse(json_encode($liste_chargements));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    #[Route('/snvlt/docbcbgfh/op/{id_foret}', name: 'app_docs_bcbgfh_json')]
    public function my_doc_bcbgfh(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_foret,
        NotificationRepository $notification,
        DocumentbcbgfhRepository $docs_bcbgfh,
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

                $mes_docs_bcbgfh = array();
                //------------------------- Filtre les bcbgfh par type Opérateur ------------------------------------- //

                //------------------------- Filtre les bcbgfh ADMIN ------------------------------------- //
                if($user->getCodeGroupe()->getId() == 1 or $this->isGranted('ROLE_DPIF_SAISIE')){
                    if ($id_foret == 0){
                        $documents_bcbgfh = $registry->getRepository(Documentbcbgfh::class)->findBy(['exercice'=>$exercice]);
                        foreach ($documents_bcbgfh as $document_bcbgfh){

                            if ($document_bcbgfh->getCodeContrat()->getCodeForet()->getCodeCantonnement()){
                                $canton = $document_bcbgfh->getCodeContrat()->getCodeForet()->getCodeCantonnement()->getNomCantonnement();
                                $d = $document_bcbgfh->getCodeContrat()->getCodeForet()->getCodeCantonnement()->getCodeDr()->getDenomination();
                            }else {
                                $canton = "-";
                                $d = "-";
                            }
                            $sigle ="-";
                            if ($document_bcbgfh->getCodeContrat()->getCodeExploitant()->getSigle()){
                                $sigle =  $document_bcbgfh->getCodeContrat()->getCodeExploitant()->getSigle();
                            }
                            $mes_docs_bcbgfh[] = array(
                                'id_document_bcbgfh'=>$document_bcbgfh->getId(),
                                'numero_docbcbgfh'=>$document_bcbgfh->getNumeroDocbcbgfh(),
                                'foret'=>$document_bcbgfh->getCodeContrat()->getCodeForet()->getDenomination(),
                                'cantonnement'=>$canton,
                                'dr'=>$d,
                                'date_delivrance'=>$document_bcbgfh->getDelivreDocbcbgfh()->format("d m Y"),
                                'etat'=>$document_bcbgfh->isEtat(),
                                'exploitant'=>$document_bcbgfh->getCodeContrat()->getCodeExploitant()->getRaisonSocialeExploitant(),
                                'sigle'=>$sigle,
                                'code_exploitant'=>$document_bcbgfh->getCodeContrat()->getCodeExploitant()->getNumeroExploitant(),
                                'volume_bcbgfh'=>round($this->getVolumebcbgfh($document_bcbgfh), 3),
                                'nb_billes'=>$this->getNbBilles($document_bcbgfh),
                                'nb_arbres'=>$this->getNbArbres($document_bcbgfh)
                            );
                        }
                    } else {
                        $foret_selectionnee = $registry->getRepository(Foret::class)->find($id_foret);

                        if ($foret_selectionnee){
                            $contrats = $registry->getRepository(ContratBcbgfh::class)->findBy(['code_foret'=>$foret_selectionnee]);
                            foreach ($contrats as $contrat){

                                $documents_bcbgfh = $registry->getRepository(Documentbcbgfh::class)->findBy(['code_contrat'=>$contrat, 'exercice'=>$exercice]);
                                foreach ($documents_bcbgfh as $document_bcbgfh){

                                    if ($document_bcbgfh->getCodeContrat()->getCodeForet()->getCodeCantonnement()){
                                        $canton = $document_bcbgfh->getCodeContrat()->getCodeForet()->getCodeCantonnement()->getNomCantonnement();
                                        $d = $document_bcbgfh->getCodeContrat()->getCodeForet()->getCodeCantonnement()->getCodeDr()->getDenomination();
                                    }else {
                                        $canton = "-";
                                        $d = "-";
                                    }
                                    $sigle ="-";
                                    if ($document_bcbgfh->getCodeContrat()->getCodeExploitant()->getSigle()){
                                        $sigle =  $document_bcbgfh->getCodeContrat()->getCodeExploitant()->getSigle();
                                    }
                                    $mes_docs_bcbgfh[] = array(
                                        'id_document_bcbgfh'=>$document_bcbgfh->getId(),
                                        'numero_docbcbgfh'=>$document_bcbgfh->getNumeroDocbcbgfh(),
                                        'foret'=>$document_bcbgfh->getCodeContrat()->getCodeForet()->getDenomination(),
                                        'cantonnement'=>$canton,
                                        'dr'=>$d,
                                        'date_delivrance'=>$document_bcbgfh->getDelivreDocbcbgfh()->format("d m Y"),
                                        'etat'=>$document_bcbgfh->isEtat(),
                                        'exploitant'=>$document_bcbgfh->getCodeContrat()->getCodeExploitant()->getRaisonSocialeExploitant(),
                                        'sigle'=>$sigle,
                                        'code_exploitant'=>$document_bcbgfh->getCodeContrat()->getCodeExploitant()->getNumeroExploitant(),
                                        'volume_bcbgfh'=>round($this->getVolumebcbgfh($document_bcbgfh), 3),
                                        'nb_billes'=>$this->getNbBilles($document_bcbgfh),
                                        'nb_arbres'=>$this->getNbArbres($document_bcbgfh)
                                    );
                                }

                            }
                        }
                    }

                    //------------------------- Filtre les bcbgfh DR ------------------------------------- //
                } else {
                    if ($user->getCodeDr()){
                        //dd($user->getCodeDr());
                        $cantonnements = $registry->getRepository(Cantonnement::class)->findBy(['code_dr'=>$user->getCodeDr()]);
                        foreach ($cantonnements as $cantonnement){
                            $forets = $registry->getRepository(Foret::class)->findBy(['code_cantonnement'=>$cantonnement]);

                            foreach ($forets as $foret){
                                $contrats = $registry->getRepository(ContratBcbgfh::class)->findBy(['code_foret'=>$foret]);
                                foreach ($contrats as $contrat){

                                        $documents_bcbgfh = $registry->getRepository(Documentbcbgfh::class)->findBy(['code_contrat'=>$contrat, 'exercice'=>$exercice]);
                                        foreach ($documents_bcbgfh as $document_bcbgfh){

                                            if ($document_bcbgfh->getCodeContrat()->getCodeForet()->getCodeCantonnement()){
                                                $canton = $document_bcbgfh->getCodeContrat()->getCodeForet()->getCodeCantonnement()->getNomCantonnement();
                                                $d = $document_bcbgfh->getCodeContrat()->getCodeForet()->getCodeCantonnement()->getCodeDr()->getDenomination();
                                            }else {
                                                $canton = "-";
                                                $d = "-";
                                            }

                                            $mes_docs_bcbgfh[] = array(
                                                'id_document_bcbgfh'=>$document_bcbgfh->getId(),
                                                'numero_docbcbgfh'=>$document_bcbgfh->getNumeroDocbcbgfh(),
                                                'foret'=>$document_bcbgfh->getCodeContrat()->getCodeForet()->getDenomination(),
                                                'cantonnement'=>$canton,
                                                'dr'=>$d,
                                                'date_delivrance'=>$document_bcbgfh->getDelivreDocbcbgfh()->format("d m Y"),
                                                'etat'=>$document_bcbgfh->isEtat(),
                                                'attribution_attribue'=>$document_bcbgfh->getCodeContrat()->isStatut(),
                                                'reprise_attribue'=>$document_bcbgfh->getCodeContrat()->isReprise(),
                                                'exploitant'=>$document_bcbgfh->getCodeContrat()->getCodeExploitant()->getRaisonSocialeExploitant(),
                                                'code_exploitant'=>$document_bcbgfh->getCodeContrat()->getCodeExploitant()->getNumeroExploitant(),
                                                'volume_bcbgfh'=>round($this->getVolumebcbgfh($document_bcbgfh), 3),
                                                'nb_billes'=>$this->getNbBilles($document_bcbgfh),
                                                'nb_arbres'=>$this->getNbArbres($document_bcbgfh)
                                            );
                                        }

                                    }
                                }

                        }

                        //------------------------- Filtre les bcbgfh DD ------------------------------------- //
                    } elseif ($user->getCodeDdef()){
                        $cantonnements = $registry->getRepository(Cantonnement::class)->findBy(['code_ddef'=>$user->getCodeDdef()]);
                        foreach ($cantonnements as $cantonnement){
                            $forets = $registry->getRepository(Foret::class)->findBy(['code_cantonnement'=>$cantonnement]);
                            foreach ($forets as $foret){
                                $contrats = $registry->getRepository(ContratBcbgfh::class)->findBy(['code_foret'=>$foret]);
                                foreach ($contrats as $contrat){

                                    $documents_bcbgfh = $registry->getRepository(Documentbcbgfh::class)->findBy(['code_contrat'=>$contrat, 'exercice'=>$exercice]);
                                    foreach ($documents_bcbgfh as $document_bcbgfh){

                                        if ($document_bcbgfh->getCodeContrat()->getCodeForet()->getCodeCantonnement()){
                                            $canton = $document_bcbgfh->getCodeContrat()->getCodeForet()->getCodeCantonnement()->getNomCantonnement();
                                            $d = $document_bcbgfh->getCodeContrat()->getCodeForet()->getCodeCantonnement()->getCodeDr()->getDenomination();
                                        }else {
                                            $canton = "-";
                                            $d = "-";
                                        }

                                        $mes_docs_bcbgfh[] = array(
                                            'id_document_bcbgfh'=>$document_bcbgfh->getId(),
                                            'numero_docbcbgfh'=>$document_bcbgfh->getNumeroDocbcbgfh(),
                                            'foret'=>$document_bcbgfh->getCodeContrat()->getCodeForet()->getDenomination(),
                                            'cantonnement'=>$canton,
                                            'dr'=>$d,
                                            'date_delivrance'=>$document_bcbgfh->getDelivreDocbcbgfh()->format("d m Y"),
                                            'etat'=>$document_bcbgfh->isEtat(),
                                            'attribution_attribue'=>$document_bcbgfh->getCodeContrat()->isStatut(),
                                            'reprise_attribue'=>$document_bcbgfh->getCodeContrat()->isReprise(),
                                            'exploitant'=>$document_bcbgfh->getCodeContrat()->getCodeExploitant()->getRaisonSocialeExploitant(),
                                            'code_exploitant'=>$document_bcbgfh->getCodeContrat()->getCodeExploitant()->getNumeroExploitant(),
                                            'volume_bcbgfh'=>round($this->getVolumebcbgfh($document_bcbgfh), 3),
                                            'nb_billes'=>$this->getNbBilles($document_bcbgfh),
                                            'nb_arbres'=>$this->getNbArbres($document_bcbgfh)
                                        );
                                    }

                                }
                            }
                        }

                        //------------------------- Filtre les bcbgfh CANTONNEMENT ------------------------------------- //
                    } elseif ($user->getCodeCantonnement()){
                        $forets = $registry->getRepository(Foret::class)->findBy(['code_cantonnement'=>$user->getCodeCantonnement()]);

                        foreach ($forets as $foret){
                            $contrats = $registry->getRepository(ContratBcbgfh::class)->findBy(['code_foret'=>$foret]);
                            foreach ($contrats as $contrat){

                                $documents_bcbgfh = $registry->getRepository(Documentbcbgfh::class)->findBy(['code_contrat'=>$contrat, 'exercice'=>$exercice]);
                                foreach ($documents_bcbgfh as $document_bcbgfh){

                                    if ($document_bcbgfh->getCodeContrat()->getCodeForet()->getCodeCantonnement()){
                                        $canton = $document_bcbgfh->getCodeContrat()->getCodeForet()->getCodeCantonnement()->getNomCantonnement();
                                        $d = $document_bcbgfh->getCodeContrat()->getCodeForet()->getCodeCantonnement()->getCodeDr()->getDenomination();
                                    }else {
                                        $canton = "-";
                                        $d = "-";
                                    }

                                    $mes_docs_bcbgfh[] = array(
                                        'id_document_bcbgfh'=>$document_bcbgfh->getId(),
                                        'numero_docbcbgfh'=>$document_bcbgfh->getNumeroDocbcbgfh(),
                                        'foret'=>$document_bcbgfh->getCodeContrat()->getCodeForet()->getDenomination(),
                                        'cantonnement'=>$canton,
                                        'dr'=>$d,
                                        'date_delivrance'=>$document_bcbgfh->getDelivreDocbcbgfh()->format("d m Y"),
                                        'etat'=>$document_bcbgfh->isEtat(),
                                        'attribution_attribue'=>$document_bcbgfh->getCodeContrat()->isStatut(),
                                        'reprise_attribue'=>$document_bcbgfh->getCodeContrat()->isReprise(),
                                        'exploitant'=>$document_bcbgfh->getCodeContrat()->getCodeExploitant()->getRaisonSocialeExploitant(),
                                        'code_exploitant'=>$document_bcbgfh->getCodeContrat()->getCodeExploitant()->getNumeroExploitant(),
                                        'volume_bcbgfh'=>round($this->getVolumebcbgfh($document_bcbgfh), 3),
                                        'nb_billes'=>$this->getNbBilles($document_bcbgfh),
                                        'nb_arbres'=>$this->getNbArbres($document_bcbgfh)
                                    );
                                }

                            }
                        }

                        //------------------------- Filtre les bcbgfh POSTE CONTROLE ------------------------------------- //
                    } elseif ($user->getCodePosteControle()){
                        $forets = $registry->getRepository(Foret::class)->findBy(['code_cantonnement'=>$user->getCodePosteControle()->getCodeCantonnement()]);
                        foreach ($forets as $foret){
                            $contrats = $registry->getRepository(ContratBcbgfh::class)->findBy(['code_foret'=>$foret]);
                            foreach ($contrats as $contrat){

                                $documents_bcbgfh = $registry->getRepository(Documentbcbgfh::class)->findBy(['code_contrat'=>$contrat, 'exercice'=>$exercice]);
                                foreach ($documents_bcbgfh as $document_bcbgfh){

                                    if ($document_bcbgfh->getCodeContrat()->getCodeForet()->getCodeCantonnement()){
                                        $canton = $document_bcbgfh->getCodeContrat()->getCodeForet()->getCodeCantonnement()->getNomCantonnement();
                                        $d = $document_bcbgfh->getCodeContrat()->getCodeForet()->getCodeCantonnement()->getCodeDr()->getDenomination();
                                    }else {
                                        $canton = "-";
                                        $d = "-";
                                    }

                                    $mes_docs_bcbgfh[] = array(
                                        'id_document_bcbgfh'=>$document_bcbgfh->getId(),
                                        'numero_docbcbgfh'=>$document_bcbgfh->getNumeroDocbcbgfh(),
                                        'foret'=>$document_bcbgfh->getCodeContrat()->getCodeForet()->getDenomination(),
                                        'cantonnement'=>$canton,
                                        'dr'=>$d,
                                        'date_delivrance'=>$document_bcbgfh->getDelivreDocbcbgfh()->format("d m Y"),
                                        'etat'=>$document_bcbgfh->isEtat(),
                                        'attribution_attribue'=>$document_bcbgfh->getCodeContrat()->isStatut(),
                                        'reprise_attribue'=>$document_bcbgfh->getCodeContrat()->isReprise(),
                                        'exploitant'=>$document_bcbgfh->getCodeContrat()->getCodeExploitant()->getRaisonSocialeExploitant(),
                                        'code_exploitant'=>$document_bcbgfh->getCodeContrat()->getCodeExploitant()->getNumeroExploitant(),
                                        'volume_bcbgfh'=>round($this->getVolumebcbgfh($document_bcbgfh), 3),
                                        'nb_billes'=>$this->getNbBilles($document_bcbgfh),
                                        'nb_arbres'=>$this->getNbArbres($document_bcbgfh)
                                    );
                                }

                            }
                        }
                        //------------------------- Filtre les bcbgfh EXPLOITANT------------------------------------- //
                    } elseif ($user->getCodeexploitant()){
                        $contrats = $registry->getRepository(ContratBcbgfh::class)->findBy(['code_exploitant'=>$user->getCodeexploitant(), 'statut'=>true]);
                        foreach ($contrats as $contrat){

                                $documents_bcbgfh = $registry->getRepository(Documentbcbgfh::class)->findBy(['code_contrat'=>$contrat, 'signature_cef'=>true, 'signature_dr'=>true, 'exercice'=>$exercice],['created_at'=>'DESC']);
                                foreach ($documents_bcbgfh as $document_bcbgfh){
                                    if ($document_bcbgfh->getCodeContrat()->getCodeForet()->getCodeCantonnement()){
                                        $canton = $document_bcbgfh->getCodeContrat()->getCodeForet()->getCodeCantonnement()->getNomCantonnement();
                                        $d = $document_bcbgfh->getCodeContrat()->getCodeForet()->getCodeCantonnement()->getCodeDr()->getDenomination();
                                    }else {
                                        $canton = "-";
                                        $d = "-";
                                    }

                                    $mes_docs_bcbgfh[] = array(
                                        'id_document_bcbgfh'=>$document_bcbgfh->getId(),
                                        'numero_docbcbgfh'=>$document_bcbgfh->getNumeroDocbcbgfh(),
                                        'foret'=>$document_bcbgfh->getCodeContrat()->getCodeForet()->getDenomination(),
                                        'cantonnement'=>$canton,
                                        'dr'=>$d,
                                        'date_delivrance'=>$document_bcbgfh->getDelivreDocbcbgfh()->format("d m Y"),
                                        'etat'=>$document_bcbgfh->isEtat(),
                                        'volume_bcbgfh'=>round($this->getVolumebcbgfh($document_bcbgfh), 3),
                                        'nb_billes'=>$this->getNbBilles($document_bcbgfh),
                                        'nb_arbres'=>$this->getNbArbres($document_bcbgfh)
                                    );
                                }

                            }


                    }


                }
                return new JsonResponse(json_encode($mes_docs_bcbgfh));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }



    }

    #[Route('/snvlt/docbcbgfh/op/pages/{id_bcbgfh}', name: 'affichage_bcbgfh_json')]
    public function affiche_bcbgfh(
        Request $request,
        int $id_bcbgfh,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentbcbgfhRepository $docs_bcbgfh,
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

                $documentbcbgfh = $registry->getRepository(Documentbcbgfh::class)->find($id_bcbgfh);
                if($documentbcbgfh){$numerodoc = $documentbcbgfh->getNumeroDocbcbgfh();}

                return $this->render('doc_stats/entetes/documentbcbgfh/affiche_bcbgfh.html.twig', [
                    'document_name'=>$documentbcbgfh,
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
    
    #[Route('/snvlt/docbcbgfh/op/pages_bcbgfh/{id_bcbgfh}', name: 'affichage_pages_bcbgfh_json')]
    public function affiche_pages_bcbgfh(
        Request $request,
        int $id_bcbgfh,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentbcbgfhRepository $docs_bcbgfh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE') or $this->isGranted('ROLE_ADMINISTRATIF'))
            {
                $doc_bcbgfh = $docs_bcbgfh->find($id_bcbgfh);
                if($doc_bcbgfh){
                    $pages_bcbgfh = $registry->getRepository(Pagebcbgfh::class)->findBy(['code_docbcbgfh'=>$doc_bcbgfh], ['id'=>'ASC']);
                    $my_bcbgfh_pages = array();

                    foreach ($pages_bcbgfh as $page){
                        $my_bcbgfh_pages[] = array(
                            'id_page'=>$page->getId(),
                            'numero_page'=>$page->getNumeroPagebcbgfh()
                        );
                    }
                    return  new JsonResponse(json_encode($my_bcbgfh_pages));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/bcbgfh/validate_form/{id_page}', name: 'validate_page_bcbgfh_json')]
    public function validate_form_page_bcbgfh(
        Request $request,
        int $id_page,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        PagebcbgfhRepository $page_bcbgfh,
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

                $page = $page_bcbgfh->find($id_page);
                if($page && !$page->isFini()){
                   if ($page->getLignepagebcbgfhs()->count() > 0)
                   {
                       $page->setFini(true);
                       $page->setEntreLje(false);
                       $page->setSoumettre(true);
                       $page->setConfirmationUsine(true);

                       $registry->getManager()->persist($page);

                        // Log TrackTimber
                        $this->administrationService->save_action(
                            $user,
                            "PAGE_BCBGFH_FC",
                            "VALIDATION CHARGEMENT",
                            new \DateTimeImmutable(),
                            "Le chargement N% ". $page->getNumeroPagebcbgfh() . " du BCBGFH-FC " . $page->getCodeDocbcbgfh()->getNumeroDocbcbgfh() . " vient d'être validé par l'agent " . $user . " de la structure [" . $page->getCodeDocbcbgfh()->getCodeContrat()->getCodeexploitant()->getRaisonSocialeExploitant() . " ]. Chargement en partance pour l'usine ". $page->getParcUsineBcbgfh()
                        );

                       $registry->getManager()->flush();
                       
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

    #[Route('/snvlt/bcbgfh/soumettre_chargement/{id_page}', name: 'soumettre_chargement')]
    public function soumettre_chargement(
        Request $request,
        int $id_page,
        UserRepository $userRepository,
        PagebcbgfhRepository $page_bcbgfh,
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

                $page = $page_bcbgfh->find($id_page);
                if($page && !$page->isFini()){
                    if ($page->getLignepagebcbgfhs()->count() > 0)
                    {
                        $page->setSoumettre(true);
                        $page->setFini(false);
                        $page->setConfirmationUsine(false);
                        $page->setEntreLje(false);

                        $page->setExercice($this->administrationService->getAnnee()->getId());
                        $registry->getManager()->persist($page);


                        // Envoi d'une notification au responsable Usine Destination, à l'exploitant forestier et aux Opérateurs MINEF concernés
                        $emailRespoExploitant = $registry->getRepository(Exploitant::class)->find($page->getCodeDocbcbgfh()->getCodeContrat()->getCodeExploitant())->getEmailPersonneRessource();

                        //Notif Responsable Structure forestière
                        $this->utils->envoiNotification(
                            $registry,
                            "Chargement Grumes N° ". $page->getNumeroPagebcbgfh() . " à valider",
                            "Le chargement N% ". $page->getNumeroPagebcbgfh() . " du BCBGFH-FC " . $page->getCodeDocbcbgfh()->getNumeroDocbcbgfh() . " vient d'être enregistré pour approbation de votre part.'" . $user ,
                            $registry->getRepository(User::class)->findOneBy(['email'=>$emailRespoExploitant]),
                            $user->getId(),
                            "validation_respo",
                            "PAGE_BCBGFH_FC",
                            $page->getId()
                        );

                        // Log TrackTimber
                        $this->administrationService->save_action(
                            $user,
                            "PAGE_BCBGFH_FC",
                            "APPROBATION CHARGEMENT",
                            new \DateTimeImmutable(),
                            "Le chargement N% ". $page->getNumeroPagebcbgfh() . " du BCBGFH-FC " . $page->getCodeDocbcbgfh()->getNumeroDocbcbgfh() . " vient d'être validé par l'agent " . $user . " de la structure [" . $user->getCodeexploitant()->getRaisonSocialeExploitant() . " ] pour approbation à son responsable."
                        );

                        $registry->getManager()->flush();

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

    #[Route('/snvlt/docbcbgfh/op/pages_bcbgfh/data/{id_page}', name: 'affichage_page_data_bcbgfh_json')]
    public function affiche_page_courante(
        Request $request,
        int $id_page,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        PagebcbgfhRepository $pages_bcbgfh,
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

                $page_bcbgfh = $pages_bcbgfh->find($id_page);
                if($page_bcbgfh){
                    $parc_usine = "";
                    $_usine = "";
                    if($page_bcbgfh->getDateChargementbcbgfh()) { $date_chargement = $page_bcbgfh->getDateChargementbcbgfh()->format('Y-m-d');} else { $date_chargement = ""; }
                    //dd($page_bcbgfh->getDateChargementbcbgfh()->format('d/m/Y'));
                    if($page_bcbgfh->getParcUsineBcbgfh()) {
                        $parc_usine = $page_bcbgfh->getParcUsineBcbgfh()->getId();
                        $_usine = $page_bcbgfh->getParcUsineBcbgfh()->getSigle();
                        if(!$_usine or $_usine="NULL"){
                            $_usine = $page_bcbgfh->getParcUsineBcbgfh()->getRaisonSocialeUsine();
                        }
                    }
                    $my_bcbgfh_page = array();
                    if ($page_bcbgfh->getPhoto()){
                        $photo = $page_bcbgfh->getPhoto();
                    } else {
                        $photo = "";
                    }
                    $my_bcbgfh_page[] = array(
                        'id_page'=>$page_bcbgfh->getId(),
                        'numero_page'=>$page_bcbgfh->getNumeroPagebcbgfh(),
                        'date_chargement'=>$date_chargement,
                        'destination'=>$page_bcbgfh->getDestinationPagebcbgfh(),
                        'parc_usine'=>$parc_usine,
                        'usine_dest'=>$_usine,
                        'transporteur'=>$page_bcbgfh->getChauffeurbcbgfh(),
                        'cout'=>$page_bcbgfh->getCoutTransportbcbgfh(),
                        'village'=>$page_bcbgfh->getVillagePagebcbgfh(),
                        'immatriculation'=>$page_bcbgfh->getImmatcamion(),
                        'fini'=>$page_bcbgfh->isFini(),
                        'photo'=>$photo
                    );

                    return  new JsonResponse(json_encode($my_bcbgfh_page));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/docbcbgfh/op/lignes_bcbgfh/data/{id_page}', name: 'affichage_ligne_bcbgfh_data_bcbgfh_json')]
    public function affiche_lignes_bcbgfh_courante(
        Request $request,
        int $id_page,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        PagebcbgfhRepository $pages_bcbgfh,
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

                $page_bcbgfh = $pages_bcbgfh->find($id_page);
                if($page_bcbgfh){
                    $lignes_bcbgfh = $registry->getRepository(Lignepagebcbgfh::class)->findBy(['code_pagebcbgfh'=>$page_bcbgfh]);
                    $my_bcbgfh_page = array();
					
					
                    foreach ($lignes_bcbgfh as $lignebcbgfh){
						$zh = "-";
						$essence = "-";
						
						if($lignebcbgfh->getZhLignepagebcbgfh())
						{
							$zh = $lignebcbgfh->getZhLignepagebcbgfh()->getZone();
						}
						
						if($lignebcbgfh->getNomEssencebcbgfh())
						{
							$essence = $lignebcbgfh->getNomEssencebcbgfh()->getNomVernaculaire();
						}
						
                        $my_bcbgfh_page[] = array(
                            'id_ligne'=>$lignebcbgfh->getId(),
                            'numero_ligne'=>$lignebcbgfh->getNumeroLignepagebcbgfh(),
                            'lettre'=>$lignebcbgfh->getLettreLignepagebcbgfh(),
                            'essence'=>$essence,
                            'x_bcbgfh'=>$lignebcbgfh->getXLignepagebcbgfh(),
                            'y_bcbgfh'=>$lignebcbgfh->getYLignepagebcbgfh(),
                            'zh_bcbgfh'=>$zh,
                            'lng_bcbgfh'=>$lignebcbgfh->getLongeurLignepagebcbgfh(),
                            'dm_bcbgfh'=>$lignebcbgfh->getDiametreLignepagebcbgfh(),
                            'cubage_bcbgfh'=>$lignebcbgfh->getCubageLignepagebcbgfh()
                        );
                    }


                    return  new JsonResponse(json_encode($my_bcbgfh_page));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    function getVolumebcbgfh(Documentbcbgfh $documentbcbgfh):float
    {
        $volumebcbgfh = 0;
        if($documentbcbgfh){
            $pagebcbgfh =$this->m->getRepository(Pagebcbgfh::class)->findBy(['code_docbcbgfh'=>$documentbcbgfh]);
            foreach ($pagebcbgfh as $page){
                $lignepages = $this->m->getRepository(Lignepagebcbgfh::class)->findBy(['code_pagebcbgfh'=>$page]);
                foreach ($lignepages as $ligne){
                    $volumebcbgfh = $volumebcbgfh +  $ligne->getCubageLignepagebcbgfh();
                }
            }
            return $volumebcbgfh;
        } else {
            return $volumebcbgfh;
        }
    }
    function getNbArbres(Documentbcbgfh $documentbcbgfh):int
    {
        $nbarbres = 0;
        if($documentbcbgfh){
            $pagebcbgfh =$this->m->getRepository(Pagebcbgfh::class)->findBy(['code_docbcbgfh'=>$documentbcbgfh]);
            foreach ($pagebcbgfh as $page){
                $lignepages = $this->m->getRepository(Lignepagebcbgfh::class)->findBy(['code_pagebcbgfh'=>$page]);
                foreach ($lignepages as $ligne){
                    if ($ligne->getLettreLignepagebcbgfh() == "A"){
                        $nbarbres = $nbarbres + 1;
                    }
                }
            }
        }
        return $nbarbres;
    }

    function getNbBilles(Documentbcbgfh $documentbcbgfh):int
    {
        $nbbilles = 0;
        if($documentbcbgfh){
            $pagebcbgfh =$this->m->getRepository(Pagebcbgfh::class)->findBy(['code_docbcbgfh'=>$documentbcbgfh]);
            foreach ($pagebcbgfh as $page){
                $lignepages = $this->m->getRepository(Lignepagebcbgfh::class)->findBy(['code_pagebcbgfh'=>$page]);
                foreach ($lignepages as $ligne){
                    $nbbilles = $nbbilles + 1;
                }
            }
        }
        return $nbbilles;
    }
    #[Route('/snvlt/docbcbgfh/op/pages_bcbgfh/data/add_lignes/{data}/{id_foret}', name: 'adddata_bcbgfh_json')]
    public function add_lignes_bcbgfh(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        string $data,
        int $id_foret,
        NotificationRepository $notification,
        PagebcbgfhRepository $pages_bcbgfh,
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

                //$page_bcbgfh = $pages_bcbgfh->find($id_page);
                if($data){
                    $lignebcbgfh = new Lignepagebcbgfh();


                    //Decoder le JSON BCBGFH-FC
                    $arraydata = json_decode($data);
                    $isSameValue = false;

                    //Recherche la foret
                    $mon_contrat = $registry->getRepository(ContratBcbgfh::class)->find((int) $arraydata->code_contrat);
                            $mes_bcbgfh = $registry->getRepository(Documentbcbgfh::class)->findBy(['code_contrat'=>$mon_contrat, 'exercice'=>$this->administrationService->getAnnee()]);
                            foreach($mes_bcbgfh as $bcbgfh){
                                $mes_pages = $registry->getRepository(Pagebcbgfh::class)->findBy(['code_docbcbgfh'=>$bcbgfh]);
                                foreach($mes_pages as $pagebcbgfh){
                                    $mes_lignes = $registry->getRepository(Lignepagebcbgfh::class)->findBy(['code_pagebcbgfh'=>$pagebcbgfh]);
                                    foreach($mes_lignes as $ligne){
                                        if ($ligne->getNumeroLignepagebcbgfh() == (int) $arraydata->numero_lignepagebcbgfh &&
                                            $ligne->getLettreLignepagebcbgfh() == $arraydata->lettre_lignepagebcbgfh
                                        ){
                                            $isSameValue = true;
                                        }
                                    }
                                }

                        }

                   // dd((int) $arraydata->numero_lignepagebcbgfh . " - " . $arraydata->lettre_lignepagebcbgfh);
                    if($isSameValue == false){

                        //dd($arraydata->numero_lignepagebcbgfh);
                        $date_jour = new \DateTime();
                        $arbre = $registry->getRepository(Lignepagecp::class)->find((int) $arraydata->numero_lignepagebcbgfh);

                        $lignebcbgfh->setNumeroLignepagebcbgfh((int) $arraydata->numero_lignepagebcbgfh);
                        $essence = $registry->getRepository(Essence::class)->find((int) $arraydata->nom_essencebcbgfh);
                        if($essence){
                            $lignebcbgfh->setNomEssencebcbgfh($essence);
                        }
                        $zh = $registry->getRepository(ZoneHemispherique::class)->find((int) $arraydata->zh_lignepagebcbgfh);
                        if($zh){
                            $lignebcbgfh->setZhLignepagebcbgfh($zh);
                        }
						
                        $lignebcbgfh->setLettreLignepagebcbgfh($arraydata->lettre_lignepagebcbgfh);
                        $lignebcbgfh->setXLignepagebcbgfh((float) $arraydata->x_lignepagebcbgfh);
                        $lignebcbgfh->setYLignepagebcbgfh((float)$arraydata->y_lignepagebcbgfh);
                        $lignebcbgfh->setLongeurLignepagebcbgfh((int) $arraydata->longeur_lignepagebcbgfh);
                        $lignebcbgfh->setDiametreLignepagebcbgfh((int) $arraydata->diametre_lignepagebcbgfh);
                        $lignebcbgfh->setCubageLignepagebcbgfh((float)$arraydata->cubage_lignepagebcbgfh);
                        $lignebcbgfh->setCreatedAt($date_jour);
                        $lignebcbgfh->setCreatedBy($user);
                        $lignebcbgfh->setCodePagebcbgfh($registry->getRepository(Pagebcbgfh::class)->find((int) $arraydata->code_pagebcbgfh));
                        if ($arbre){
                            $lignebcbgfh->setCodeLigneCp($arbre);
                        }

						$lignebcbgfh->setExercice($this->administrationService->getAnnee());
                        $registry->getManager()->persist($lignebcbgfh);


                        //Mise à jour de l'arbre et la bille CP
						if ($arbre){
							if ($lignebcbgfh->getLettreLignepagebcbgfh() == "A"){
								$arbre->setAUtlise(true);
								}elseif ($lignebcbgfh->getLettreLignepagebcbgfh() == "B"){
										$arbre->setBUtilise(true);
								}elseif ($lignebcbgfh->getLettreLignepagebcbgfh() == "C"){
										$arbre->setCUtilise(true);
								}
                        $registry->getManager()->persist($arbre);
						}
                        

                        $this->administrationService->save_action(
                            $user,
                            'LIGNE_PAGE_BCBGFH_FC',
                            'AJOUT',
                            new \DateTimeImmutable(),
                            'La bille N° ' . $lignebcbgfh->getNumeroLignepagebcbgfh(). $lignebcbgfh->getLettreLignepagebcbgfh() . " a été enregistrée par ". $user
                        );

                        $registry->getManager()->flush();
                        $response = array();
                        $response[] = array(
                            'code_bcbgfh'=>Lignepagebcbgfh::LIGNE_BCBGFH_ADDED_SUCCESSFULLY,
                            'html'=>''
                        );

                    } else {
                        $response[] = array(
                            'code_bcbgfh'=>'SAME_NUMBER',
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

    #[Route('/snvlt/docbcbgfh/op/pages_bcbgfh/data/edit/{id_page}/{data}', name: 'edit_page_bcbgfh_json')]
    public function edit_page_bcbgfh(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        string $data,
        int $id_page,
        NotificationRepository $notification,
        PagebcbgfhRepository $pages_bcbgfh,
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

                //$page_bcbgfh = $pages_bcbgfh->find($id_page);
                if($data){
                    $pagebcbgfh = $registry->getRepository(Pagebcbgfh::class)->find($id_page);

                    if ($pagebcbgfh){
                        //Decoder le JSON BCBGFH-FC
                        $arraydata = json_decode($data);

                        //dd($arraydata->numero_lignepagebcbgfh);
                        $date_jour = new \DateTime();
                        $pagebcbgfh->setDestinationPagebcbgfh(strtoupper($arraydata->destination_pagebcbgfh));
                        $pagebcbgfh->setParcUsineBcbgfh($registry->getRepository(Usine::class)->find((int)  $arraydata->parc_usine_bcbgfh));


                        $date_chargement = new \DateTime();
                        $date_chargement =  $arraydata->date_chargementbcbgfh;



                        $pagebcbgfh->setImmatcamion(strtoupper($arraydata->immatcamion));
                        $pagebcbgfh->setCoutTransportbcbgfh((int) $arraydata->cout_transportbcbgfh);
                        $pagebcbgfh->setVillagePagebcbgfh(strtoupper($arraydata->village_pagebcbgfh));
                        $pagebcbgfh->setChauffeurbcbgfh(strtoupper($arraydata->chauffeurbcbgfh));
                        $pagebcbgfh->setDateChargementbcbgfh(\DateTime::createFromFormat('Y-m-d', $date_chargement));
                        $pagebcbgfh->setUpdatedAt(new \DateTime());
                        $pagebcbgfh->setUpdatedBy($user);

                        $pagebcbgfh->setCodeDocbcbgfh($registry->getRepository(Documentbcbgfh::class)->find((int) $arraydata->code_docbcbgfh));

                        if($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE')){
                            $pagebcbgfh->setConfirmationUsine(true);
                            $pagebcbgfh->setFini(true);
                            $pagebcbgfh->setEntreLje(false);
                        }
                        $registry->getManager()->persist($pagebcbgfh);
                        $registry->getManager()->flush();

                        return  new JsonResponse([
                            'code'=>Pagebcbgfh::PAGE_BCBGFH_EDITED_SUCCESSFULLY,
                            'html'=>''
                        ]);
                    }

                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/detail_bcbgfh_loading/details/{id_page}', name:'app_my_loadings')]
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
                    $pagebcbgfh = $registry->getRepository(Pagebcbgfh::class)->find($id_page);
                    if ($pagebcbgfh) {


                            return $this->render('doc_stats/entetes/documentbcbgfh/details_chargement.html.twig',
                                [
                                    'liste_menus'=>$menus->findOnlyParent(),
                                    "all_menus"=>$menus->findAll(),
                                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                                    'mes_notifs'=>$notifications->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                                    'groupe'=>$code_groupe,
                                    'chargement'=>$pagebcbgfh,
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

    #[Route('/snvlt/detail_bcbgfh_loading/accept/{id_page?0}', name:'accept_chargement')]
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

                $pagebcbgfh = $registry->getRepository(Pagebcbgfh::class)->find($id_page);
                if ($pagebcbgfh) {

                    $pagebcbgfh->setConfirmationUsine(true);
                    $registry->getManager()->persist($pagebcbgfh);
                    $registry->getManager()->flush();

                    //Save Log

                    $this->administrationService->save_action(
                        $user,
                        "PAGE_BCBGFH_FC",
                        "ACCEPTAION_CHARGEMENT_USINE",
                        new \DateTimeImmutable(),
                        "Chargement ". $pagebcbgfh->getCodeDocbcbgfh()->getTypeDocument()->getDenomination() . " N° " . $pagebcbgfh->getCodeDocbcbgfh()->getNumeroDocbcbgfh() . " - Feuillet N° ". $pagebcbgfh->getNumeroPagebcbgfh() . " en provenance de " . $pagebcbgfh->getCodeDocbcbgfh()->getCodeContrat()->getCodeExploitant()->getRaisonSocialeExploitant() . " [Forêt " . $pagebcbgfh->getCodeDocbcbgfh()->getCodeContrat()->getCodeForet()->getDenomination() . "] a été accepté par l'usine " . $user->getCodeindustriel()->getRaisonSocialeUsine() . " {User : " . $user . "}"
                    );

                    //envoi Notification à l'exploitant
                    $this->utils->envoiNotification(
                        $registry,
                        "Infos sur le Chargement N° " . $pagebcbgfh->getNumeroPagebcbgfh() . " [" . $pagebcbgfh->getCodeDocbcbgfh()->getCodeContrat()->getCodeForet()->getDenomination() . "]",
                        "Chargement ". $pagebcbgfh->getCodeDocbcbgfh()->getTypeDocument()->getDenomination() . " N° " . $pagebcbgfh->getCodeDocbcbgfh()->getNumeroDocbcbgfh() . " - Feuillet N° ". $pagebcbgfh->getNumeroPagebcbgfh() . " a été accepté par l'usine  " . $pagebcbgfh->getParcUsineBcbgfh()->getRaisonSocialeUsine(),
                        $registry->getRepository(User::class)->findOneBy(['email'=>$pagebcbgfh->getCodeDocbcbgfh()->getCodeContrat()->getCodeExploitant()->getEmailPersonneRessource()]),
                        $user,
                        "app_my_loadings",
                        "PAGE_BCBGFH_FC",
                        $pagebcbgfh->getId()
                    );

                    //envoi Notification à la DR de l'usine
                        $this->utils->envoiNotification(
                            $registry,
                            "Infos sur le Chargement N° " . $pagebcbgfh->getNumeroPagebcbgfh() . " [" . $pagebcbgfh->getCodeDocbcbgfh()->getCodeContrat()->getCodeForet()->getDenomination() . "]",
                            "Chargement ". $pagebcbgfh->getCodeDocbcbgfh()->getTypeDocument()->getDenomination() . " N° " . $pagebcbgfh->getCodeDocbcbgfh()->getNumeroDocbcbgfh() . " - Feuillet N° ". $pagebcbgfh->getNumeroPagebcbgfh() . " a été accepté par l'usine  " . $pagebcbgfh->getParcUsineBcbgfh()->getRaisonSocialeUsine(),
                            $registry->getRepository(User::class)->findOneBy(['email'=>$pagebcbgfh->getParcUsineBcbgfh()->getCodeCantonnement()->getCodeDr()->getEmailPersonneRessource()]),
                            $user,
                            "app_my_loadings",
                            "PAGE_BCBGFH_FC",
                            $pagebcbgfh->getId()
                        );

                    //envoi Notification à la DD de l'usine si elle existe
                    if ($user->getCodeindustriel()->getCodeCantonnement()->getCodeDdef()->getId() > 0){
                    if ($user->getCodeindustriel()->getCodeCantonnement()->getCodeDdef()->getEmailPersonneRessource()){
                       // dd($user->getCodeindustriel()->getCodeCantonnement()->getCodeDdef()->getId());
                        $this->utils->envoiNotification(
                            $registry,
                            "Infos sur le Chargement N° " . $pagebcbgfh->getNumeroPagebcbgfh() . " [" . $pagebcbgfh->getCodeDocbcbgfh()->getCodeContrat()->getCodeForet()->getDenomination() . "]",
                            "Chargement ". $pagebcbgfh->getCodeDocbcbgfh()->getTypeDocument()->getDenomination() . " N° " . $pagebcbgfh->getCodeDocbcbgfh()->getNumeroDocbcbgfh() . " - Feuillet N° ". $pagebcbgfh->getNumeroPagebcbgfh() . " a été accepté par l'usine  " . $pagebcbgfh->getParcUsineBcbgfh()->getRaisonSocialeUsine(),
                            $registry->getRepository(User::class)->findOneBy(['email'=>$pagebcbgfh->getParcUsineBcbgfh()->getCodeCantonnement()->getCodeDdef()->getEmailPersonneRessource()]),
                            $user,
                            "app_my_loadings",
                            "PAGE_BCBGFH_FC",
                            $pagebcbgfh->getId()
                        );
                      }
                    }

                    //envoi Notification au cantonnement de l'usine
                    $this->utils->envoiNotification(
                        $registry,
                        "Infos sur le Chargement N° " . $pagebcbgfh->getNumeroPagebcbgfh() . " [" . $pagebcbgfh->getCodeDocbcbgfh()->getCodeContrat()->getCodeForet()->getDenomination() . "]",
                        "Chargement ". $pagebcbgfh->getCodeDocbcbgfh()->getTypeDocument()->getDenomination() . " N° " . $pagebcbgfh->getCodeDocbcbgfh()->getNumeroDocbcbgfh() . " - Feuillet N° ". $pagebcbgfh->getNumeroPagebcbgfh() . " a été accepté par l'usine  " . $pagebcbgfh->getParcUsineBcbgfh()->getRaisonSocialeUsine(),
                        $registry->getRepository(User::class)->findOneBy(['email'=>$pagebcbgfh->getParcUsineBcbgfh()->getCodeCantonnement()->getEmailPersonneRessource()]),
                        $user,
                        "app_my_loadings",
                        "PAGE_BCBGFH_FC",
                        $pagebcbgfh->getId()
                    );

                    return new JsonResponse(json_encode(Pagebcbgfh::PAGE_BCBGFH_ACCEPTED));


                } else {
                    return new JsonResponse(json_encode("BAD NOTIFICATION"));
                }

            }else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/detail_bcbgfh_loading/{id_notification?0}', name:'app_my_loadings')]
    public function details_chargements(
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
                    $pagebcbgfh = $registry->getRepository(Pagebcbgfh::class)->find($notification->getRelatedToId());
                    if ($pagebcbgfh) {


                        return $this->render('doc_stats/entetes/documentbcbgfh/details_chargement.html.twig',
                            [
                                'liste_menus'=>$menus->findOnlyParent(),
                                "all_menus"=>$menus->findAll(),
                                'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                                'mes_notifs'=>$notifications->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                                'groupe'=>$code_groupe,
                                'chargement'=>$pagebcbgfh,
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

    #[Route('/snvlt/numbcbgfh/dispo/{id_page}', name:'num_bcbgfh_dispo')]
    public function num_bcbgfh_dispo(
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

                $contrat = $registry->getRepository(Pagebcbgfh::class)->find($id_page)->getCodeDocbcbgfh()->getCodeContrat();
//dd($reprise);
//                if ($contrat){
//                    $doccp = $registry->getRepository(Documentcp::class)->findBy(['code_reprise'=>$reprise]);
//                    foreach ($doccp as $doc){
//                        $pagecps = $registry->getRepository(Pagecp::class)->findBy(['code_doccp'=>$doc]);
//                        //dd($pagecps);
//                        foreach ($pagecps as $pagecp){
//                            $lignes = $registry->getRepository(Lignepagecp::class)->findBy(['code_pagecp'=>$pagecp, 'fut_abandon'=>false]);
//
//                            foreach ($lignes as $ligne){
//                                if (!$ligne->isFutAbandon()){
//                                    if ($ligne->isAUtlise() == false or  $ligne->isBUtilise() == false or  $ligne->isCUtilise() == false){
//                                        $liste_numeros[] = array(
//                                            'id_ligne'=>$ligne->getId(),
//                                            'num'=>$ligne->getNumeroArbrecp()
//                                        );
//                                    }
//                                }
//                            }
//                        }
//
//                    }
//                    return new JsonResponse(json_encode($liste_numeros));
//
//                }
//                else {
//                    return new JsonResponse(json_encode(false));
//                }


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/numbcbgfh/arbre_infos/{id_bille}', name:'info_bille_bcbgfh')]
    public function info_bille_bcbgfh(
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

    #[Route('/snvlt/numbcbgfh/dispo/lettres/{id_bille}', name:'lettres_bille_bcbgfh')]
    public function lettres_bille_bcbgfh(
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

    #[Route('/snvlt/docbcbgfh/validate_chargement/page/{id_page}', name: 'affiche_page_courante_validation')]
    public function affiche_page_courante_validation(
        Request $request,
        int $id_page,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        PagebcbgfhRepository $pages_bcbgfh,
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

                $page_bcbgfh = $pages_bcbgfh->find($id_page);
                $my_bcbgfh_page = array();
                if($page_bcbgfh){
                    $parc_usine = "";
                    if($page_bcbgfh->getDateChargementbcbgfh()) { $date_chargement = $page_bcbgfh->getDateChargementbcbgfh()->format('d/m/Y');} else { $date_chargement = ""; }
                    //dd($page_bcbgfh->getDateChargementbcbgfh()->format('d/m/Y'));
                    if($page_bcbgfh->getParcUsineBcbgfh()) {
                        if($page_bcbgfh->getParcUsineBcbgfh()->getSigle()) {
                            $parc_usine = $page_bcbgfh->getParcUsineBcbgfh()->getSigle();
                        } else {
                            $parc_usine = $page_bcbgfh->getParcUsineBcbgfh()->getRaisonSocialeUsine();
                        }
                    }


                    $lignes_bcbgfh = $registry->getRepository(Lignepagebcbgfh::class)->findBy(['code_pagebcbgfh'=>$page_bcbgfh]);

                    foreach($lignes_bcbgfh as $ligne){
                        $my_bcbgfh_page[] = array(
                            'id_page'=>$page_bcbgfh->getId(),
                            'foret'=>$page_bcbgfh->getCodeDocbcbgfh()->getCodeContrat()->getCodeForet()->getDenomination(),
                            'numero_page'=>$page_bcbgfh->getNumeroPagebcbgfh(),
                            'numero_doc'=>$page_bcbgfh->getCodeDocbcbgfh()->getNumeroDocbcbgfh(),
                            'date_chargement'=>$date_chargement,
                            'destination'=>$parc_usine. " - [".$page_bcbgfh->getDestinationPagebcbgfh()."]" ,
                            'id_arbre'=>$ligne->getId(),
                            'numero_arbre'=>$ligne->getNumeroLignepagebcbgfh(),
                            'lettre_arbre'=>$ligne->getLettreLignepagebcbgfh(),
                            'essence'=>$ligne->getNomEssencebcbgfh()->getNomVernaculaire(),
                            'zone'=>$ligne->getZhLignepagebcbgfh()->getZone(),
                            'x'=>$ligne->getXLignepagebcbgfh(),
                            'y'=>$ligne->getYLignepagebcbgfh(),
                            'lng'=>$ligne->getLongeurLignepagebcbgfh(),
                            'dm'=>$ligne->getDiametreLignepagebcbgfh(),
                            'vol'=>$ligne->getCubageLignepagebcbgfh(),
                            'photo'=>$page_bcbgfh->getPhoto(),
                        );
                    }


                    return  new JsonResponse(json_encode($my_bcbgfh_page));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    /*Photo du feuillet*/
    #[Route('/snvlt/docbcbgfh/op/pages/p/{id_page?0}', name: 'photo_page_bcbgfh')]
    public function photo_page_bcbgfh(
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

                $pagebcbgfh = $registry->getRepository(Pagebcbgfh::class)->find($id_page);

                    $form = $this->createForm(PagebcbgfhType::class, $pagebcbgfh);

                    $form->handleRequest($request);

                    if ($form->isSubmitted() && $form->isValid()) {

                        if($pagebcbgfh){
                        $fichier = $form->get('photo')->getData();

                        if ($fichier) {$originalFilename = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);


                            $safeFilename = $this->slugger->slug($originalFilename);
                            $newFilename = $this->utils->uniqidReal(30).'.'.$fichier->guessExtension();

                            // Move the file to the directory where brochures are stored
                            try {
                                $fichier->move(
                                    $this->getParameter('images_bcbgfh_directory'),
                                    $newFilename
                                );
                                //sleep(4);
                            } catch (FileException $e) {
                                // ... handle exception if something happens during file upload
                            }

                            // updates the 'brochureFilename' property to store the PDF file name
                            // instead of its contents
                            $pagebcbgfh->setPhoto($newFilename);

                        }

                        //dd($this->getParameter('prospections_csv_directory'),);

                        $pagebcbgfh->setUpdatedAt(new \DateTime());
                        $pagebcbgfh->setUpdatedBy($user);


                        $manager = $registry->getManager();
                        $manager->persist($pagebcbgfh);
                        $manager->flush();


                        return $this->redirectToRoute("affichage_bcbgfh_json", [
                            'id_bcbgfh' => $pagebcbgfh->getCodeDocbcbgfh()->getId()
                        ]);

                    }
                } else {
                        return $this->render('doc_stats/entetes/documentbcbgfh/photo_bcbgfh.html.twig', [

                            'liste_menus'=>$menus->findOnlyParent(),
                            "all_menus"=>$menus->findAll(),
                            'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                            'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                            'groupe'=>$code_groupe,
                            'liste_parent'=>$permissions,
                            'form'=>$form->createView(),
                            'feuillet'=>$pagebcbgfh
                        ]);
                    }


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    /*Photo du feuillet*/
    #[Route('/snvlt/docbcbgfh/op/f/p/{id_page?0}', name: 'photo_feuillet_bcbgfh')]
    public function photo_feuillet_bcbgfh(
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

                $pagebcbgfh = $registry->getRepository(Pagebcbgfh::class)->find($id_page);
                if($pagebcbgfh){
                    return $this->render('doc_stats/entetes/documentbcbgfh/feuillet_photo.html.twig', [

                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                        'groupe'=>$code_groupe,
                        'liste_parent'=>$permissions,
                        'feuillet'=>$pagebcbgfh
                    ]);
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
	
	#[Route('snvlt/bcbgfh/edit/{id_bille}/B2', name:'edit_bille')]
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

                $bille = $registry->getRepository(Lignepagebcbgfh::class)->find($id_bille);

                if ($bille){
                    $infos_bille[] = array(
                        'id_ligne'=>$bille->getId(),
                        'numero'=>$bille->getNumeroLignepagebcbgfh(),
                        'essence'=>$bille->getNomEssencebcbgfh()->getId(),
                        'zh'=>$bille->getZhLignepagebcbgfh()->getId(),
                        'x'=>$bille->getXLignepagebcbgfh(),
                        'y'=>$bille->getYLignepagebcbgfh(),
                        'lettre'=>$bille->getLettreLignepagebcbgfh(),
                        'lng'=>$bille->getLongeurLignepagebcbgfh(),
                        'dm'=>$bille->getDiametreLignepagebcbgfh(),
                        'cubage'=>$bille->getCubageLignepagebcbgfh()
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
	
	#[Route('/snvlt/docbcbgfh/op/data/edit_lignes/{data}/{id_bille}', name: 'editdata_bcbgfh_json')]
    public function edit_lignes_bcbgfh(
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

                $bille = $registry->getRepository(Lignepagebcbgfh::class)->find($id_bille);
                if ($bille){
                    if($data){
                        $arraydata = json_decode($data);

                            $bille->setNumeroLignepagebcbgfh((int)$arraydata->numero_lignepagebcbgfh);
                            $bille->setLettreLignepagebcbgfh($arraydata->lettre_lignepagebcbgfh);
                            $bille->setXLignepagebcbgfh((float) $arraydata->x_lignepagebcbgfh);
                            $bille->setYLignepagebcbgfh((float)$arraydata->y_lignepagebcbgfh);
                            $bille->setLongeurLignepagebcbgfh((int) $arraydata->longeur_lignepagebcbgfh);
                            $bille->setDiametreLignepagebcbgfh((int) $arraydata->diametre_lignepagebcbgfh);
                            $bille->setCubageLignepagebcbgfh((float)$arraydata->cubage_lignepagebcbgfh);
                            $bille->setUpdatedAt($date_jour);
                            $bille->setUpdatedBy($user);

                            $registry->getManager()->persist($bille);
                            $registry->getManager()->flush();

                            $this->administrationService->save_action(
                                $user,
                                'LIGNE_PAGE_BCBGFH_FC',
                                'MODIFICATION',
                                new \DateTimeImmutable(),
                                'La bille N° ' . $bille->getNumeroLignepagebcbgfh(). $bille->getLettreLignepagebcbgfh() . " a été modifiée par ". $user
                            );


                            $response[] = array(
                                'code_bcbgfh'=>Lignepagebcbgfh::LIGNE_BCBGFH_ADDED_SUCCESSFULLY
                            );

                        } else {
                                $response[] = array(
                                    'code_bcbgfh' => 'SAME_NUMBER'
                                );
                            }
                        return  new JsonResponse(json_encode($response));
                    }
                

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
	
	
	#[Route('/snvlt/docbcbgfh/bcbgfh_cubagetotal', name: 'bcbgfh_cubagetotal')]
    public function bcbgfh_cubagetotal(
        Request $request,
        UserRepository $userRepository,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN'))
            {

                $response = array();
                $cubage= 0;
                $billes = $registry->getRepository(Lignepagebcbgfh::class)->findBy(['exercice'=>$this->administrationService->getAnnee()]);
                foreach ($billes as $bille){
                    $cubage = $cubage + $bille->getCubageLignepagebcbgfh();
                }



                        $response[] = array(
                            'cubage'=>round($cubage, 0)
                        );


                    return  new JsonResponse(json_encode($response));



            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
	
	#[Route('snvlt/bcbgfh/del/{id_bille}/B1', name:'del_bille')]
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

                $bille = $registry->getRepository(Lignepagebcbgfh::class)->find($id_bille);

                if ($bille){
                    $registry->getManager()->remove($bille);
                    $registry->getManager()->flush();


                    $this->administrationService->save_action(
                        $this->getUser(),
                        'LIGNE_PAGE_BCBGFH_FC',
                        'SUPPRESSION',
                        new \DateTimeImmutable(),
                        'La bille N° ' . $bille->getNumeroLignepagebcbgfh(). $bille->getLettreLignepagebcbgfh() . " a été supprimée par ". $this->getUser()
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
}