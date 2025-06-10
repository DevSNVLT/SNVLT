<?php

namespace App\Controller\DocStats\Entetes;

use App\Controller\Services\AdministrationService;
use App\Controller\Services\Utils;
use App\Entity\Autorisation\Attribution;
use App\Entity\Autorisation\AttributionPv;
use App\Entity\Autorisation\AutorisationPv;
use App\Entity\Autorisation\Reprise;
use App\Entity\DocStats\Entetes\Documentbcbp;
use App\Entity\DocStats\Entetes\Documentbrh;
use App\Entity\DocStats\Pages\Pagebcbp;
use App\Entity\DocStats\Pages\Pagebrh;
use App\Entity\DocStats\Saisie\Lignepagebcbp;
use App\Entity\DocStats\Saisie\Lignepagebrh;
use App\Entity\DocStats\Saisie\Lignepagebtgu;
use App\Entity\DocStats\Saisie\Lignepagecp;
use App\Entity\References\Cantonnement;
use App\Entity\References\Essence;
use App\Entity\References\Exploitant;
use App\Entity\References\Foret;
use App\Entity\References\SousPrefecture;
use App\Entity\References\Usine;
use App\Entity\References\ZoneHemispherique;
use App\Entity\User;
use App\Form\DocStats\Pages\PagebcbpType;
use App\Form\DocStats\Pages\PagebrhType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\DocStats\Entetes\DocumentbcbpRepository;
use App\Repository\DocStats\Entetes\DocumentbrhRepository;
use App\Repository\DocStats\Pages\PagebcbpRepository;
use App\Repository\DocStats\Pages\PagebrhRepository;
use App\Repository\DocStats\Pages\PagebtguRepository;
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

class DocumentbcbpController extends AbstractController
{

    public function __construct(private ManagerRegistry $m,
                                private AdministrationService $administrationService,
                                private SluggerInterface $slugger,
                                private Utils $utils)
    {
    }

    #[Route('/doc/stats/entetes/docbcbp', name: 'app_op_docbcbp')]
    public function index(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentbcbpRepository $docs_bcbp,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')  )
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                return $this->render('doc_stats/entetes/documentbcbp/index.html.twig', [
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

    #[Route('/snvlt/docbcbp/bcbp/pages/{id_bcbp}', name: 'affichage_bcbp_json')]
    public function affiche_bcbp(
        Request $request,
        int $id_bcbp,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentbcbpRepository $docs_bcbp,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_EXPORTATEUR'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $numerodoc = "";

                $documentbcbp = $registry->getRepository(Documentbcbp::class)->find($id_bcbp);
                if($documentbcbp){$numerodoc = $documentbcbp->getNumeroDocbcbp();}

                return $this->render('doc_stats/entetes/documentbcbp/affiche_bcbp.html.twig', [
                    'document_name'=>$documentbcbp,
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'groupe'=>$code_groupe,
                    'liste_parent'=>$permissions,
                    'essences'=>$registry->getRepository(Essence::class)->findBy([], ['nom_vernaculaire'=>'ASC']),
                    'zones'=>$registry->getRepository(ZoneHemispherique::class)->findBy([], ['zone'=>'ASC']),
                    'usines'=>$registry->getRepository(Usine::class)->findBy([], ['raison_sociale_usine'=>'ASC']),
                    'villes'=>$registry->getRepository(SousPrefecture::class)->findBy([], ['nom_sousprefecture'=>'ASC'])
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/docbcbp/op', name: 'app_docs_bcbp_json')]
    public function my_doc_bcbp(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentbcbpRepository $docs_bcbp,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')  )
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $mes_docs_bcbp = array();
                //------------------------- Filtre les bcbp par type Opérateur ------------------------------------- //

                //------------------------- Filtre les BCBP ADMIN ------------------------------------- //
                if($user->getCodeGroupe()->getId() == 1){
                    $documents_bcbp = $registry->getRepository(Documentbcbp::class)->findAll();





                    foreach ($documents_bcbp as $document_bcbp){

                        if ($document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getCodeCantonnement()){
                            $canton = $document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getCodeCantonnement()->getNomCantonnement();
                            $d = $document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getCodeCantonnement()->getCodeDr()->getDenomination();
                        }else {
                            $canton = "-";
                            $d = "-";
                        }

                        $mes_docs_bcbp[] = array(
                            'id_document_bcbp'=>$document_bcbp->getId(),
                            'numero_docbcbp'=>$document_bcbp->getNumeroDocbcbp(),
                            'parcelle'=>$document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getDenomination(),
                            'cantonnement'=>$canton,
                            'dr'=>$d,
                            'date_delivrance'=>$document_bcbp->getDelivreDocbcbp()->format("d m Y"),
                            'etat'=>$document_bcbp->isEtat(),
                            'exploitant'=>$document_bcbp->getCodeAutorisationPv()->getCodeExploitant()->getRaisonSocialeExploitant(),
                            'code_exploitant'=>$document_bcbp->getCodeAutorisationPv()->getCodeExploitant()->getNumeroExploitant(),
                            'attributaire'=>$document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getRaisonSociale(),
                            'volume_bcbp'=>$this->getVolumebcbp($document_bcbp)
                        );
                    }
                    //------------------------- Filtre les BCBP DR ------------------------------------- //
                } else {
                    if ($user->getCodeDr()){
                        //dd($user->getCodeDr());
                        $cantonnements = $registry->getRepository(Cantonnement::class)->findBy(['code_dr'=>$user->getCodeDr()]);
                        foreach ($cantonnements as $cantonnement){
                            $parcelles = $registry->getRepository(Foret::class)->findBy(['code_cantonnement'=>$cantonnement, 'code_type_foret'=>3]);

                            foreach ($parcelles as $parcelle){
                                $attributions = $registry->getRepository(AttributionPv::class)->findBy(['code_parcelle'=>$parcelle]);
                                foreach ($attributions as $attribution){
                                    $reprises = $registry->getRepository(AutorisationPv::class)->findBy(['code_attribution_pv'=>$attribution]);
                                    foreach ($reprises as $reprise){
                                        $documents_bcbp = $registry->getRepository(Documentbcbp::class)->findBy(['code_autorisation_pv'=>$reprise]);
                                        foreach ($documents_bcbp as $document_bcbp){
                                            if ($document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getCodeCantonnement()){
                                                $canton = $document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getCodeCantonnement()->getNomCantonnement();
                                                $d = $document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getCodeCantonnement()->getCodeDr()->getDenomination();
                                            }else {
                                                $canton = "-";
                                                $d = "-";
                                            }
                                            $mes_docs_bcbp[] = array(
                                                'id_document_bcbp'=>$document_bcbp->getId(),
                                                'numero_docbcbp'=>$document_bcbp->getNumeroDocbcbp(),
                                                'parcelle'=>$document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getDenomination(),
                                                'cantonnement'=>$document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getCodeCantonnement()->getNomCantonnement(),
                                                'dr'=>$document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getCodeCantonnement()->getCodeDr()->getDenomination(),
                                                'date_delivrance'=>$document_bcbp->getDelivreDocbcbp()->format("d m Y"),
                                                'etat'=>$document_bcbp->isEtat(),
                                                'exploitant'=>$document_bcbp->getCodeAutorisationPv()->getCodeExploitant()->getRaisonSocialeExploitant(),
                                                'code_exploitant'=>$document_bcbp->getCodeAutorisationPv()->getCodeExploitant()->getNumeroExploitant(),
                                                'attributaire'=>$document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getRaisonSociale(),
                                                'volume_bcbp'=>$this->getVolumebcbp($document_bcbp)
                                            );
                                        }

                                    }
                                }
                            }
                        }

                        //------------------------- Filtre les BCBP DD ------------------------------------- //
                    } elseif ($user->getCodeDdef()){
                        //dd($user->getCodeDr());
                        $cantonnements = $registry->getRepository(Cantonnement::class)->findBy(['code_ddef'=>$user->getCodeDdef()]);
                        foreach ($cantonnements as $cantonnement){
                            $parcelles = $registry->getRepository(Foret::class)->findBy(['code_cantonnement'=>$cantonnement, 'code_type_foret'=>3]);

                            foreach ($parcelles as $parcelle){
                                $attributions = $registry->getRepository(AttributionPv::class)->findBy(['code_parcelle'=>$parcelle]);
                                foreach ($attributions as $attribution){
                                    $reprises = $registry->getRepository(AutorisationPv::class)->findBy(['code_attribution_pv'=>$attribution]);
                                    foreach ($reprises as $reprise){
                                        $documents_bcbp = $registry->getRepository(Documentbcbp::class)->findBy(['code_autorisation_pv'=>$reprise]);
                                        foreach ($documents_bcbp as $document_bcbp){
                                            if ($document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getCodeCantonnement()){
                                                $canton = $document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getCodeCantonnement()->getNomCantonnement();
                                                $d = $document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getCodeCantonnement()->getCodeDr()->getDenomination();
                                            }else {
                                                $canton = "-";
                                                $d = "-";
                                            }
                                            $mes_docs_bcbp[] = array(
                                                'id_document_bcbp'=>$document_bcbp->getId(),
                                                'numero_docbcbp'=>$document_bcbp->getNumeroDocbcbp(),
                                                'parcelle'=>$document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getDenomination(),
                                                'cantonnement'=>$document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getCodeCantonnement()->getNomCantonnement(),
                                                'dr'=>$document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getCodeCantonnement()->getCodeDr()->getDenomination(),
                                                'date_delivrance'=>$document_bcbp->getDelivreDocbcbp()->format("d m Y"),
                                                'etat'=>$document_bcbp->isEtat(),
                                                'exploitant'=>$document_bcbp->getCodeAutorisationPv()->getCodeExploitant()->getRaisonSocialeExploitant(),
                                                'code_exploitant'=>$document_bcbp->getCodeAutorisationPv()->getCodeExploitant()->getNumeroExploitant(),
                                                'attributaire'=>$document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getRaisonSociale(),
                                                'volume_bcbp'=>$this->getVolumebcbp($document_bcbp)
                                            );
                                        }

                                    }
                                }
                            }
                        }

                        //------------------------- Filtre les BCBP CANTONNEMENT ------------------------------------- //
                    } elseif ($user->getCodeCantonnement()){
                        $parcelles = $registry->getRepository(Foret::class)->findBy(['code_cantonnement'=>$user->getCodeCantonnement(), 'code_type_foret'=>3]);

                        foreach ($parcelles as $parcelle){
                            $attributions = $registry->getRepository(AttributionPv::class)->findBy(['code_parcelle'=>$parcelle]);
                            foreach ($attributions as $attribution){
                                $reprises = $registry->getRepository(AutorisationPv::class)->findBy(['code_attribution_pv'=>$attribution]);
                                foreach ($reprises as $reprise){
                                    $documents_bcbp = $registry->getRepository(Documentbcbp::class)->findBy(['code_autorisation_pv'=>$reprise]);
                                    foreach ($documents_bcbp as $document_bcbp){
                                        if ($document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getCodeCantonnement()){
                                            $canton = $document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getCodeCantonnement()->getNomCantonnement();
                                            $d = $document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getCodeCantonnement()->getCodeDr()->getDenomination();
                                        }else {
                                            $canton = "-";
                                            $d = "-";
                                        }
                                        $mes_docs_bcbp[] = array(
                                            'id_document_bcbp'=>$document_bcbp->getId(),
                                            'numero_docbcbp'=>$document_bcbp->getNumeroDocbcbp(),
                                            'parcelle'=>$document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getDenomination(),
                                            'cantonnement'=>$document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getCodeCantonnement()->getNomCantonnement(),
                                            'dr'=>$document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getCodeCantonnement()->getCodeDr()->getDenomination(),
                                            'date_delivrance'=>$document_bcbp->getDelivreDocbcbp()->format("d m Y"),
                                            'etat'=>$document_bcbp->isEtat(),
                                            'exploitant'=>$document_bcbp->getCodeAutorisationPv()->getCodeExploitant()->getRaisonSocialeExploitant(),
                                            'code_exploitant'=>$document_bcbp->getCodeAutorisationPv()->getCodeExploitant()->getNumeroExploitant(),
                                            'attributaire'=>$document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getRaisonSociale(),
                                            'volume_bcbp'=>$this->getVolumebcbp($document_bcbp)
                                        );
                                    }

                                }
                            }
                        }

                        //------------------------- Filtre les BCBP POSTE CONTROLE ------------------------------------- //
                    } elseif ($user->getCodePosteControle()){
                        $parcelles = $registry->getRepository(Foret::class)->findBy(['code_cantonnement'=>$user->getCodePosteControle()->getCodeCantonnement(), 'code_type_foret'=>3]);

                        foreach ($parcelles as $parcelle){
                            $attributions = $registry->getRepository(AttributionPv::class)->findBy(['code_parcelle'=>$parcelle]);
                            foreach ($attributions as $attribution){
                                $reprises = $registry->getRepository(AutorisationPv::class)->findBy(['code_attribution_pv'=>$attribution]);
                                foreach ($reprises as $reprise){
                                    $documents_bcbp = $registry->getRepository(Documentbcbp::class)->findBy(['code_autorisation_pv'=>$reprise]);
                                    foreach ($documents_bcbp as $document_bcbp){
                                        if ($document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getCodeCantonnement()){
                                            $canton = $document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getCodeCantonnement()->getNomCantonnement();
                                            $d = $document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getCodeCantonnement()->getCodeDr()->getDenomination();
                                        }else {
                                            $canton = "-";
                                            $d = "-";
                                        }
                                        $mes_docs_bcbp[] = array(
                                            'id_document_bcbp'=>$document_bcbp->getId(),
                                            'numero_docbcbp'=>$document_bcbp->getNumeroDocbcbp(),
                                            'parcelle'=>$document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getDenomination(),
                                            'cantonnement'=>$document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getCodeCantonnement()->getNomCantonnement(),
                                            'dr'=>$document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getCodeCantonnement()->getCodeDr()->getDenomination(),
                                            'date_delivrance'=>$document_bcbp->getDelivreDocbcbp()->format("d m Y"),
                                            'etat'=>$document_bcbp->isEtat(),
                                            'exploitant'=>$document_bcbp->getCodeAutorisationPv()->getCodeExploitant()->getRaisonSocialeExploitant(),
                                            'code_exploitant'=>$document_bcbp->getCodeAutorisationPv()->getCodeExploitant()->getNumeroExploitant(),
                                            'attributaire'=>$document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getRaisonSociale(),
                                            'volume_bcbp'=>$this->getVolumebcbp($document_bcbp)
                                        );
                                    }

                                }
                            }
                        }
                        //------------------------- Filtre les BCBP EXPLOITANT------------------------------------- //
                    } elseif ($user->getCodeexploitant()){

                            $reprises = $registry->getRepository(AutorisationPv::class)->findBy(['code_exploitant'=>$user->getCodeexploitant()]);
                            foreach ($reprises as $reprise){
                                $documents_bcbp = $registry->getRepository(Documentbcbp::class)->findBy(['code_autorisation_pv'=>$reprise, 'signature_dr'=>true, 'signature_cef'=>true],['created_at'=>'DESC']);
                                foreach ($documents_bcbp as $document_bcbp){
                                    if ($document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getCodeCantonnement()){
                                        $canton = $document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getCodeCantonnement()->getNomCantonnement();
                                        $d = $document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getCodeCantonnement()->getCodeDr()->getDenomination();
                                    }else {
                                        $canton = "-";
                                        $d = "-";
                                    }
                                    $mes_docs_bcbp[] = array(
                                        'id_document_bcbp'=>$document_bcbp->getId(),
                                        'numero_docbcbp'=>$document_bcbp->getNumeroDocbcbp(),
                                        'parcelle'=>$document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getDenomination(),
                                        'cantonnement'=>$document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getCodeCantonnement()->getNomCantonnement(),
                                        'dr'=>$document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getCodeCantonnement()->getCodeDr()->getDenomination(),
                                        'date_delivrance'=>$document_bcbp->getDelivreDocbcbp()->format("d m Y"),
                                        'etat'=>$document_bcbp->isEtat(),
                                        'exploitant'=>$document_bcbp->getCodeAutorisationPv()->getCodeExploitant()->getRaisonSocialeExploitant(),
                                        'code_exploitant'=>$document_bcbp->getCodeAutorisationPv()->getCodeExploitant()->getNumeroExploitant(),
                                        'attributaire'=>$document_bcbp->getCodeAutorisationPv()->getCodeAttributionPv()->getRaisonSociale(),
                                        'volume_bcbp'=>$this->getVolumebcbp($document_bcbp)
                                    );
                                }


                        }
                    }


                }
                return new JsonResponse(json_encode($mes_docs_bcbp));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }



    }

    #[Route('/snvlt/docbcbp/op/pages/{id_bcbp}', name: 'affichage_bcbppages_json')]
    public function affiche_bcbp_pages(
        Request $request,
        int $id_bcbp,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
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

                $documentbcbp = $registry->getRepository(Documentbcbp::class)->find($id_bcbp);
                if($documentbcbp){$numerodoc = $documentbcbp->getNumeroDocbcbp();}
                return $this->render('doc_stats/entetes/documentbcbp/affiche_bcbp.html.twig', [
                    'document_name'=>$documentbcbp,
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


    #[Route('/snvlt/docbcbp/op/pages_bcbp/{id_bcbp}', name: 'affichage_pages_bcbp_json')]
    public function affiche_pages_bcbp(
        Request $request,
        int $id_bcbp,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentbcbpRepository $docs_bcbp,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE') or $this->isGranted('ROLE_ADMINISTRATIF'))
            {
                $doc_bcbp = $docs_bcbp->find($id_bcbp);
                if($doc_bcbp){
                    $pages_bcbp = $registry->getRepository(Pagebcbp::class)->findBy(['code_docbcbp'=>$doc_bcbp], ['id'=>'ASC']);
                    $my_bcbp_pages = array();

                    foreach ($pages_bcbp as $page){
                        $my_bcbp_pages[] = array(
                            'id_page'=>$page->getId(),
                            'numero_page'=>$page->getNumeroPagebcbp()
                        );
                    }
                    return  new JsonResponse(json_encode($my_bcbp_pages));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/docbcbp/op/pages_bcbp/data/{id_page}', name: 'affichage_page_data_bcbp_json')]
    public function affiche_page_courante(
        Request $request,
        int $id_page,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        PagebcbpRepository $pages_bcbp,
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

                $page_bcbp = $pages_bcbp->find($id_page);
                if($page_bcbp){
                    $parc_usine = 0;
                    $_usine = "";
                    if($page_bcbp->getDateChargement()) { $date_chargement = $page_bcbp->getDateChargement()->format('Y-m-d');} else { $date_chargement = ""; }
                    //dd($page_bcbp->getDateChargementbcbp()->format('d/m/Y'));
                    if($page_bcbp->getParcUsine()) {
                        $parc_usine = $page_bcbp->getParcUsine()->getId();
                        $_usine = $page_bcbp->getParcUsine()->getSigle();
                        if(!$_usine or $_usine="NULL"){
                            $_usine = $page_bcbp->getParcUsine()->getRaisonSocialeUsine();
                        }
                    }
                    if ($page_bcbp->getEssence()){
                        $essence = $page_bcbp->getEssence()->getId();
                    } else {
                        $essence = 0;
                    }
                    $my_bcbp_page = array();
                    if ($page_bcbp->getPhoto()){
                        $photo = $page_bcbp->getPhoto();
                    } else {
                        $photo = "";
                    }
                    $sp = $registry->getRepository(SousPrefecture::class)->find((int) $page_bcbp->getDestination());
                    if ($sp){
                        $destination =  $sp->getNomSousprefecture();
                    } else {
                        $destination = 0;
                    }
                    $my_bcbp_page[] = array(
                        'id_page'=>$page_bcbp->getId(),
                        'numero_page'=>$page_bcbp->getNumeroPagebcbp(),
                        'date_chargement'=>$date_chargement,
                        'destination'=>$destination,
                        'dest_id'=>$page_bcbp->getDestination(),
                        'parc_usine'=>$parc_usine,
                        'usine_dest'=>$_usine,
                        'transporteur'=>$page_bcbp->getTransporteur(),
                        'conducteur'=>$page_bcbp->getConducteur(),
                        'cout'=>$page_bcbp->getCout(),
                        'immatriculation'=>$page_bcbp->getImmatcamion(),
                        'fini'=>$page_bcbp->isFini(),
                        'photo'=>$photo,
                        'essence'=>$essence,
                        'nb_billes'=>$page_bcbp->getNbBilles(),
                        'volume'=>$page_bcbp->getVolume()
                    );

                    return  new JsonResponse(json_encode($my_bcbp_page));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }



    #[Route('/snvlt/docbcbp/op/pages_bcbp/data/edit/{id_page}/{data}', name: 'edit_page_bcbp_json')]
    public function edit_page_bcbp(
        Request $request,
        UserRepository $userRepository,
        string $data,
        int $id_page,
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

                if($data){
                    $pagebcbp = $registry->getRepository(Pagebcbp::class)->find($id_page);

                    if ($pagebcbp){
                        //Decoder le JSON bcbp
                        $arraydata = json_decode($data);

                        $date_jour = new \DateTime();
                        $pagebcbp->setDestination(strtoupper($arraydata->destination_pagebcbp));
                        $pagebcbp->setParcUsine($registry->getRepository(Usine::class)->find((int)  $arraydata->parc_usine_bcbp));
                        $pagebcbp->setEssence($registry->getRepository(Essence::class)->find((int)  $arraydata->essence));


                        $date_chargement = new \DateTime();
                        $date_chargement =  $arraydata->date_chargementbcbp;



                        $pagebcbp->setImmatcamion(strtoupper($arraydata->immatcamion));
                        $pagebcbp->setCout((int) $arraydata->cout_transportbcbp);
                        $pagebcbp->setTransporteur(strtoupper($arraydata->transporteur));
                        $pagebcbp->setConducteur(strtoupper($arraydata->chauffeurbcbp));
                        $pagebcbp->setDateChargement(\DateTime::createFromFormat('Y-m-d', $date_chargement));
                        $pagebcbp->setUpdatedAt(new \DateTime());
                        $pagebcbp->setUpdatedBy($user);

                        $pagebcbp->setCodeDocbcbp($registry->getRepository(Documentbcbp::class)->find((int) $arraydata->code_docbcbp));

                        if($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE')){
                            $pagebcbp->setConfirmationUsine(true);
                            $pagebcbp->setFini(true);
                            $pagebcbp->setEntreLje(false);
                        }
                        $pagebcbp->setNbBilles($this->getNbBillesPageBcbp($pagebcbp));
                        $pagebcbp->setVolume($this->getVolumePageBcbp($pagebcbp));

                        $registry->getManager()->persist($pagebcbp);
                        $registry->getManager()->flush();

                        return  new JsonResponse([
                            'code'=>"PAGE_BCBP_EDITED_SUCCESSFULLY",
                            'html'=>''
                        ]);
                    }

                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/docbcbp/op/pages_bcbp/data/add_lignes/{data}/{id_foret}', name: 'adddata_bcbp_json')]
    public function add_lignes_bcbp(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        string $data,
        int $id_foret,
        NotificationRepository $notification,
        PagebcbpRepository $pages_bcbp,
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

                //$page_bcbp = $pages_bcbp->find($id_page);
                if($data){
                    $lignebcbp = new Lignepagebcbp();


                    //Decoder le JSON BCBP
                    $arraydata = json_decode($data);
                    $isSameValue = false;

                    //Recherche la foret
                    $mes_attributions = $registry->getRepository(AttributionPv::class)->findBy(['code_parcelle'=>$registry->getRepository(Foret::class)->find($id_foret)]);
                    foreach($mes_attributions as $attribution){
                        $mes_reprises =$registry->getRepository(AutorisationPv::class)->findBy(['code_attribution_pv'=>$attribution]);
                        foreach($mes_reprises as $reprise){
                            $mes_bcbp = $registry->getRepository(Documentbcbp::class)->findBy(['code_autorisation_pv'=>$reprise]);
                            foreach($mes_bcbp as $bcbp){
                                $mes_pages = $registry->getRepository(Pagebcbp::class)->findBy(['code_docbcbp'=>$bcbp]);
                                foreach($mes_pages as $pagebcbp){
                                    $mes_lignes = $registry->getRepository(Lignepagebcbp::class)->findBy(['code_pagebcbp'=>$pagebcbp]);
                                    foreach($mes_lignes as $ligne){
                                        if ($ligne->getNumeroBille() == (int) $arraydata->numero_lignepagebcbp &&
                                            $ligne->getLettre() == $arraydata->lettre_lignepagebcbp
                                        ){
                                            $isSameValue = true;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if($isSameValue == false){

                        $date_jour = new \DateTime();

                            $lignebcbp->setNumeroBille((int) $arraydata->numero_lignepagebcbp);

                            $zh = $registry->getRepository(ZoneHemispherique::class)->find((int) $arraydata->zh_lignepagebcbp);
                            if($zh){
                                $lignebcbp->setZhLignepagebcbp($zh);
                            }


                        $lignebcbp->setLettre($arraydata->lettre_lignepagebcbp);
                        $lignebcbp->setX((float) $arraydata->x_lignepagebcbp);
                        $lignebcbp->setY((float)$arraydata->y_lignepagebcbp);
                        $lignebcbp->setLng((int) $arraydata->longeur_lignepagebcbp);
                        $lignebcbp->setDm((int) $arraydata->diametre_lignepagebcbp);
                        $lignebcbp->setVolume((float)$arraydata->cubage_lignepagebcbp);
                        $lignebcbp->setCreatedAt($date_jour);
                        $lignebcbp->setCreatedBy($user);
                        $lignebcbp->setCodePagebcbp($registry->getRepository(Pagebcbp::class)->find((int) $arraydata->code_pagebcbp));
                        $lignebcbp->setEssence($lignebcbp->getCodePagebcbp()->getEssence());

                        $lignebcbp->setExercice($this->administrationService->getAnnee());
                        $registry->getManager()->persist($lignebcbp);


                        $this->administrationService->save_action(
                            $user,
                            'LIGNE_PAGE_BCBP',
                            'AJOUT',
                            new \DateTimeImmutable(),
                            'La bille N° ' . $lignebcbp->getNumeroBille(). $lignebcbp->getLettre() . " a été enregistrée par ". $user
                        );

                        $registry->getManager()->flush();
                        $response = array();
                        $response[] = array(
                            'code_bcbp'=>'LIGNE_BCBP_ADDED_SUCCESSFULLY',
                            'html'=>''
                        );

                    } else {
                        $response[] = array(
                            'code_bcbp'=>'SAME_NUMBER',
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

    function getVolumebcbp(Documentbcbp $documentbcbp):float
    {
        $volumebcbp = 0;
        if($documentbcbp){
            $pagebcbp =$this->m->getRepository(Pagebcbp::class)->findBy(['code_docbcbp'=>$documentbcbp]);
            foreach ($pagebcbp as $page){
                $lignepages = $this->m->getRepository(Lignepagebcbp::class)->findBy(['code_pagebcbp'=>$page]);
                foreach ($lignepages as $ligne){
                    $volumebcbp = $volumebcbp +  $ligne->getVolume();
                }
            }
            return $volumebcbp;
        } else {
            return $volumebcbp;
        }
    }

    function getVolumePageBcbp(Pagebcbp $page):float
    {
        $volume = 0;
        if($page){
            $lignepages = $this->m->getRepository(Lignepagebcbp::class)->findBy(['code_pagebcbp'=>$page]);
            foreach ($lignepages as $ligne){
                $volume = $volume +  $ligne->getVolume();
            }
        }
        return $volume;
    }

    function getNbBillesPageBcbp(Pagebcbp $page):float
    {
        $nb_billes = 0;
        if($page){
            $lignepages = $this->m->getRepository(Lignepagebcbp::class)->findBy(['code_pagebcbp'=>$page]);
            foreach ($lignepages as $ligne){
                $nb_billes = $nb_billes +  1;
            }
        }
        return $nb_billes;
    }

    #[Route('snvlt/docbcbp/op/lignes_bcbp/delete_ligne/{id_ligne}', name:'delete_ligne_bcbp')]
    public function delete_ligne_bcbp(
        ManagerRegistry $registry,
        Request $request,
        int $id_ligne
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_EXPLOITANT')){
                $user = $registry->getRepository(User::class)->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                $infos = array();

                $lignepagebcbp = $registry->getRepository(Lignepagebcbp::class)->find($id_ligne);
                //dd($lignepagebcbp);
                if ($lignepagebcbp){

                    $registry->getManager()->remove($lignepagebcbp);

                    $registry->getManager()->flush();
                    $this->administrationService->save_action(
                        $user,
                        'LIGNE BCBP',
                        'SUPPRESSION',
                        new \DateTimeImmutable(),
                        'La bille N° ' . $lignepagebcbp->getNumeroBille().$lignepagebcbp->getLettre()." vient d'être supprimée par " .$this->getUser()
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

    /*Photo du feuillet*/
    #[Route('/snvlt/docbcbp/op/pages/p/{id_page?0}', name: 'photo_page_bcbp')]
    public function photo_page_bcbp(
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

                $pagebcbp = $registry->getRepository(Pagebcbp::class)->find($id_page);

                $form = $this->createForm(PagebcbpType::class, $pagebcbp);

                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {

                    if($pagebcbp){
                        $fichier = $form->get('photo')->getData();

                        if ($fichier) {$originalFilename = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);


                            $safeFilename = $this->slugger->slug($originalFilename);
                            $newFilename = $this->utils->uniqidReal(30).'.'.$fichier->guessExtension();

                            // Move the file to the directory where brochures are stored
                            try {
                                $fichier->move(
                                    $this->getParameter('images_bcbp_directory'),
                                    $newFilename
                                );
                                //sleep(4);
                            } catch (FileException $e) {
                                // ... handle exception if something happens during file upload
                            }

                            // updates the 'brochureFilename' property to store the PDF file name
                            // instead of its contents
                            $pagebcbp->setPhoto($newFilename);

                        }

                        //dd($this->getParameter('prospections_csv_directory'),);

                        $pagebcbp->setUpdatedAt(new \DateTime());
                        $pagebcbp->setUpdatedBy($user);


                        $manager = $registry->getManager();
                        $manager->persist($pagebcbp);
                        $manager->flush();


                        return $this->redirectToRoute("affichage_bcbp_json", [
                            'id_bcbp' => $pagebcbp->getCodeDocbcbp()->getId()
                        ]);

                    }
                } else {
                    return $this->render('doc_stats/entetes/documentbcbp/photo_bcbp.html.twig', [

                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                        'groupe'=>$code_groupe,
                        'liste_parent'=>$permissions,
                        'form'=>$form->createView(),
                        'feuillet'=>$pagebcbp
                    ]);
                }


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/bcbp/soumettre_chargement/{id_page}', name: 'soumettre_chargement_bcbp')]
    public function soumettre_chargement_bcbp(
        Request $request,
        int $id_page,
        UserRepository $userRepository,
        PagebcbpRepository $page_bcbp,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE'))
            {
                $user = $userRepository->find($this->getUser());

                $page = $registry->getRepository(Pagebcbp::class)->find($id_page);

                        //dd($page->isSoumettre());
                        $page->setSoumettre(true);
                        $page->setFini(false);
                        $page->setConfirmationUsine(false);
                        $page->setEntreLje(false);
                        $registry->getManager()->persist($page);
                        $registry->getManager()->flush();

                        // Envoi d'une notification au responsable Usine Destination, à l'exploitant forestier et aux Opérateurs MINEF concernés
                        $emailRespoExploitant = $registry->getRepository(Exploitant::class)->find($page->getCodeDocbcbp()->getCodeAutorisationPv()->getCodeExploitant())->getEmailPersonneRessource();

                        //Notif Responsable Structure forestière
                        if ($registry->getRepository(User::class)->findOneBy(['email'=>$emailRespoExploitant])){
                            $this->utils->envoiNotification(
                                $registry,
                                "Chargement Grumes N° ". $page->getNumeroPagebcbp() . " à valider",
                                "Le chargement N% ". $page->getNumeroPagebcbp() . " du BCBP " . $page->getCodeDocbcbp()->getNumeroDocbcbp() . " vient d'être enregistré pour approbation de votre part.'" . $user ,
                                $registry->getRepository(User::class)->findOneBy(['email'=>$emailRespoExploitant]),
                                $user->getId(),
                                "validation_respo",
                                "PAGE_BCBP",
                                $page->getId()
                            );
                        }


                        // Log TrackTimber
                        $this->administrationService->save_action(
                            $user,
                            "PAGE_BCBP",
                            "APPROBATION CHARGEMENT",
                            new \DateTimeImmutable(),
                            "Le chargement N% ". $page->getNumeroPagebcbp() . " du BCBP " . $page->getCodeDocbcbp()->getNumeroDocbcbp() . " vient d'être soumi par l'agent " . $user . " de la structure [" . $user->getCodeexploitant()->getRaisonSocialeExploitant() . " ] pour approbation à son responsable."
                        );



                        return  new JsonResponse(json_encode($page->isSoumettre()));



            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/docbcbp/validate_chargement/page/{id_page}', name: 'affiche_page_courante_validation_bcbp')]
    public function affiche_page_courante_validation_bcbp(
        Request $request,
        int $id_page,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        PagebcbpRepository $pages_bcbp,
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

                $page_bcbp = $pages_bcbp->find($id_page);
                $my_bcbp_page = array();
                if($page_bcbp){
                    $parc_usine = "";
                    if($page_bcbp->getDateChargement()) { $date_chargement = $page_bcbp->getDateChargement()->format('d/m/Y');} else { $date_chargement = ""; }
                    //dd($page_bcbp->getDateChargementbcbp()->format('d/m/Y'));
                    if($page_bcbp->getParcUsine()) {
                        if($page_bcbp->getParcUsine()->getSigle()) {
                            $parc_usine = $page_bcbp->getParcUsine()->getSigle();
                        } else {
                            $parc_usine = $page_bcbp->getParcUsine()->getRaisonSocialeUsine();
                        }
                    }


                    $lignes_bcbp = $registry->getRepository(Lignepagebcbp::class)->findBy(['code_pagebcbp'=>$page_bcbp]);
                    if ($page_bcbp->getCodeDocbcbp()->getCodeAutorisationPv()->getCodeExploitant()->getSigle()){
                        $exploitant = $page_bcbp->getCodeDocbcbp()->getCodeAutorisationPv()->getCodeExploitant()->getSigle();
                    } else {
                        $exploitant = $page_bcbp->getCodeDocbcbp()->getCodeAutorisationPv()->getCodeExploitant()->getRaisonSocialeExploitant();
                    }

                    if($page_bcbp->getEssence()) {
                        if($page_bcbp->getEssence()->getNomVernaculaire()) {
                            $essence = $page_bcbp->getEssence()->getNomVernaculaire();
                        } else {
                            $essence = "-";
                        }
                    }
                    foreach($lignes_bcbp as $ligne){
                        $my_bcbp_page[] = array(
                            'id_page'=>$page_bcbp->getId(),
                            'foret'=>$page_bcbp->getCodeDocbcbp()->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getDenomination(),
                            'numero_page'=>$page_bcbp->getNumeroPagebcbp(),
                            'numero_doc'=>$page_bcbp->getCodeDocbcbp()->getNumeroDocbcbp(),
                            'date_chargement'=>$date_chargement,
                            'destination'=>$parc_usine. " - [".$page_bcbp->getDestination()."]" ,
                            'id_arbre'=>$ligne->getId(),
                            'numero_arbre'=>$ligne->getNumeroBille(),
                            'lettre_arbre'=>$ligne->getLettre(),
                            'zone'=>$ligne->getZhLignepagebcbp()->getZone(),
                            'x'=>$ligne->getX(),
                            'y'=>$ligne->getY(),
                            'lng'=>$ligne->getLng(),
                            'dm'=>$ligne->getDm(),
                            'vol'=>$ligne->getVolume(),
                            'photo'=>$page_bcbp->getPhoto(),
                            'exploitant'=>$exploitant,
                            'parc_usine'=>$parc_usine,
                            'essence'=>$essence
                        );
                    }


                    return  new JsonResponse(json_encode($my_bcbp_page));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/bcbp/validate_form/{id_page}', name: 'validate_page_bcbp_json')]
    public function validate_form_page_bcbp(
        Request $request,
        int $id_page,
        UserRepository $userRepository,
        PagebcbpRepository $page_bcbp,
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

                $page = $page_bcbp->find($id_page);
                if($page && !$page->isFini()){
                    if ($page->getLignepagebcbps()->count() > 0)
                    {
                        $page->setFini(true);
                        $page->setEntreLje(false);
                        $page->setSoumettre(true);

                        if ($this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE')){
                            $page->setConfirmationUsine(true);
                        }

                        $registry->getManager()->persist($page);
                        $registry->getManager()->flush();

                        // Log SNVLT
                        $this->administrationService->save_action(
                            $user,
                            "PAGE_BCBP",
                            "VALIDATION CHARGEMENT",
                            new \DateTimeImmutable(),
                            "Le chargement N% ". $page->getNumeroPagebcbp() . " du BCBP " . $page->getCodeDocbcbp()->getNumeroDocbcbp() . " vient d'être validé par l'agent " . $user . " de la structure [" . $page->getCodeDocbcbp()->getCodeAutorisationPv()->getCodeexploitant()->getRaisonSocialeExploitant() . " ]. Chargement en partance pour l'usine ". $page->getParcUsine()
                        );

                        //Notification App Respo Usine
                        if ($page->getParcUsine()->getEmailPersonneRessource()){
                            $respo_usine = $registry->getRepository(User::class)->findOneBy([
                                'email'=>$page->getParcUsine()->getEmailPersonneRessource()
                            ]);
                            if ($respo_usine){
                                $this->utils->envoiNotification(
                                    $registry,
                                    "Chargement N° ".  $page->getNumeroPagebcbp() . " [" . $page->getCodeDocbcbp()->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getDenomination() . "] en destination de votre usine " ,
                                    "Chargement ". $page->getCodeDocbcbp()->getTypeDocument()->getAbv() . " N° " . $page->getCodeDocbcbp()->getNumeroDocbcbp() . " - Feuillet N° ". $page->getNumeroPagebcbp() . " est en transit vers votre usine  ",
                                    $respo_usine,
                                    $user->getId(),
                                    "app_my_loadings_notifs",
                                    "PAGE_BCBP",
                                    $page->getId()
                                );
                            }

                        }


                        // Notification Email Respo Usine

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

    #[Route('/snvlt/detail_bcbp_loading/details/{id_page}', name:'app_my_bcbp_loadings')]
    public function my_bcbp_loadings(
        ManagerRegistry $registry,
        Request $request,
        int $id_page,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        UserRepository $userRepository,
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
                $pagebcbp = $registry->getRepository(Pagebcbp::class)->find($id_page);
                if ($pagebcbp) {


                    return $this->render('doc_stats/entetes/documentbcbp/details_chargement.html.twig',
                        [
                            'liste_menus'=>$menus->findOnlyParent(),
                            "all_menus"=>$menus->findAll(),
                            'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                            'mes_notifs'=>$notifications->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                            'groupe'=>$code_groupe,
                            'chargement'=>$pagebcbp,
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
    #[Route('/snvlt/detail_bcbp_loading/accept/{id_page?0}', name:'accept_chargement_bcbp')]
    public function accepter_chargement_bcbp(
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

                $pagebcbp = $registry->getRepository(Pagebcbp::class)->find($id_page);
                if ($pagebcbp) {

                    $pagebcbp->setConfirmationUsine(true);
                    $registry->getManager()->persist($pagebcbp);
                    $registry->getManager()->flush();

                    //Save Log

                    $this->administrationService->save_action(
                        $user,
                        "PAGE_BCBP",
                        "ACCEPTAION_CHARGEMENT_USINE",
                        new \DateTimeImmutable(),
                        "Chargement ". $pagebcbp->getCodeDocbcbp()->getTypeDocument()->getAbv() . " N° " . $pagebcbp->getCodeDocbcbp()->getNumeroDocbcbp() . " - Feuillet N° ". $pagebcbp->getNumeroPagebcbp() . " en provenance de " . $pagebcbp->getCodeDocbcbp()->getCodeAutorisationPv()->getCodeExploitant()->getRaisonSocialeExploitant() . " [Forêt " . $pagebcbp->getCodeDocbcbp()->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getDenomination() . "] a été accepté par l'usine " . $user->getCodeindustriel()->getRaisonSocialeUsine() . " {User : " . $user . "}"
                    );

                    //envoi Notification à l'exploitant
                    if ($pagebcbp->getCodeDocbcbp()->getCodeAutorisationPv()->getCodeExploitant()->getEmailPersonneRessource()){
                        $this->utils->envoiNotification(
                            $registry,
                            "Infos sur le Chargement N° " . $pagebcbp->getNumeroPagebcbp() . " [" . $pagebcbp->getCodeDocbcbp()->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getDenomination() . "]",
                            "Chargement ". $pagebcbp->getCodeDocbcbp()->getTypeDocument()->getDenomination() . " N° " . $pagebcbp->getCodeDocbcbp()->getNumeroDocbcbp() . " - Feuillet N° ". $pagebcbp->getNumeroPagebcbp() . " a été accepté par l'usine  " . $pagebcbp->getParcUsineBrh()->getRaisonSocialeUsine(),
                            $registry->getRepository(User::class)->findOneBy(['email'=>$pagebcbp->getCodeDocbcbp()->getCodeAutorisationPv()->getCodeExploitant()->getEmailPersonneRessource()]),
                            $user,
                            "app_my_loadings_notifs",
                            "PAGE_BCBP",
                            $pagebcbp->getId()
                        );
                    }


                    //envoi Notification à la DR de l'usine
                    if ($pagebcbp->getParcUsine()->getCodeCantonnement()->getCodeDr()->getEmailPersonneRessource()){
                        $this->utils->envoiNotification(
                            $registry,
                            "Infos sur le Chargement N° " . $pagebcbp->getNumeroPagebcbp() . " [" . $pagebcbp->getCodeDocbcbp()->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getDenomination() . "]",
                            "Chargement ". $pagebcbp->getCodeDocbcbp()->getTypeDocument()->getDenomination() . " N° " . $pagebcbp->getCodeDocbcbp()->getNumeroDocbcbp() . " - Feuillet N° ". $pagebcbp->getNumeroPagebcbp() . " a été accepté par l'usine  " . $pagebcbp->getParcUsineBrh()->getRaisonSocialeUsine(),
                            $registry->getRepository(User::class)->findOneBy(['email'=>$pagebcbp->getParcUsine()->getCodeCantonnement()->getCodeDr()->getEmailPersonneRessource()]),
                            $user,
                            "app_my_loadings_notifs",
                            "PAGE_BCBP",
                            $pagebcbp->getId()
                        );
                    }


                    //envoi Notification à la DD de l'usine si elle existe
                    if ($user->getCodeindustriel()->getCodeCantonnement()->getCodeDdef()->getId() > 0){
                        if ($user->getCodeindustriel()->getCodeCantonnement()->getCodeDdef()->getEmailPersonneRessource()){
                            // dd($user->getCodeindustriel()->getCodeCantonnement()->getCodeDdef()->getId());
                            $this->utils->envoiNotification(
                                $registry,
                                "Infos sur le Chargement N° " . $pagebcbp->getNumeroPagebcbp() . " [" . $pagebcbp->getCodeDocbcbp()->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getDenomination() . "]",
                                "Chargement ". $pagebcbp->getCodeDocbcbp()->getTypeDocument()->getDenomination() . " N° " . $pagebcbp->getCodeDocbcbp()->getNumeroDocbcbp() . " - Feuillet N° ". $pagebcbp->getNumeroPagebcbp() . " a été accepté par l'usine  " . $pagebcbp->getParcUsineBrh()->getRaisonSocialeUsine(),
                                $registry->getRepository(User::class)->findOneBy(['email'=>$pagebcbp->getParcUsine()->getCodeCantonnement()->getCodeDdef()->getEmailPersonneRessource()]),
                                $user,
                                "app_my_loadings_notifs",
                                "PAGE_BCBP",
                                $pagebcbp->getId()
                            );
                        }
                    }

                    //envoi Notification au cantonnement de l'usine
                    if ($pagebcbp->getParcUsine()->getCodeCantonnement()->getEmailPersonneRessource()){
                        $this->utils->envoiNotification(
                            $registry,
                            "Infos sur le Chargement N° " . $pagebcbp->getNumeroPagebcbp() . " [" . $pagebcbp->getCodeDocbcbp()->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getDenomination() . "]",
                            "Chargement ". $pagebcbp->getCodeDocbcbp()->getTypeDocument()->getDenomination() . " N° " . $pagebcbp->getCodeDocbcbp()->getNumeroDocbcbp() . " - Feuillet N° ". $pagebcbp->getNumeroPagebcbp() . " a été accepté par l'usine  " . $pagebcbp->getParcUsineBrh()->getRaisonSocialeUsine(),
                            $registry->getRepository(User::class)->findOneBy(['email'=>$pagebcbp->getParcUsine()->getCodeCantonnement()->getEmailPersonneRessource()]),
                            $user,
                            "app_my_loadings_notifs",
                            "PAGE_BCBGFH",
                            $pagebcbp->getId()
                        );
                    }


                    return new JsonResponse(json_encode("CHARGEMENT_BCBP_ACCEPTE"));

                } else {
                    return new JsonResponse(json_encode("BAD NOTIFICATION"));
                }

            }else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/docbcbp/op/lignes_bcbp/data/{id_page}', name: 'affichage_ligne_bcbp_data_bcbp_json')]
    public function affiche_lignes_bcbp_courante(
        Request $request,
        int $id_page,
        UserRepository $userRepository,
        PagebcbpRepository $pages_bcbp,
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

                $page_bcbp = $pages_bcbp->find($id_page);
                if($page_bcbp){
                    $lignes_bcbp = $registry->getRepository(Lignepagebcbp::class)->findBy(['code_pagebcbp'=>$page_bcbp]);
                    $my_bcbp_page = array();


                    foreach ($lignes_bcbp as $lignebcbp){
                        $zh = "-";
                        $essence = "-";

                        if($lignebcbp->getZhLignepagebcbp())
                        {
                            $zh = $lignebcbp->getZhLignepagebcbp()->getZone();
                        }

                        if($lignebcbp->getEssence())
                        {
                            $essence = $lignebcbp->getEssence()->getNomVernaculaire();
                        }

                        $my_bcbp_page[] = array(
                            'id_ligne'=>$lignebcbp->getId(),
                            'numero_ligne'=>$lignebcbp->getNumeroBille(),
                            'lettre'=>$lignebcbp->getLettre(),
                            'essence'=>$essence,
                            'x'=>$lignebcbp->getX(),
                            'y'=>$lignebcbp->getY(),
                            'zh'=>$zh,
                            'lng'=>$lignebcbp->getLng(),
                            'dm'=>$lignebcbp->getDm(),
                            'volume'=>$lignebcbp->getVolume(),
                            'fini'=>$page_bcbp->isFini(),
                            'utilisateur'=>$lignebcbp->getCreatedBy(),
                        );
                    }


                    return  new JsonResponse(json_encode($my_bcbp_page));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/docbtgu/op/lignes_btgu/data/{id_page}', name: 'affichage_ligne_btgu_data_btgu_json')]
    public function affiche_lignes_btgu_courante(
        Request $request,
        int $id_page,
        UserRepository $userRepository,
        PagebtguRepository $pages_btgu,
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

                $page_btgu = $pages_btgu->find($id_page);
                if($page_btgu){
                    $lignes_btgu = $registry->getRepository(Lignepagebtgu::class)->findBy(['code_pagebtgu'=>$page_btgu]);
                    $my_btgu_page = array();


                    foreach ($lignes_btgu as $lignebtgu){
                        $zh = "-";
                        $essence = "-";

                        if($lignebtgu->getZh())
                        {
                            $zh = $lignebtgu->getZh()->getZone();
                        }

                        if($lignebtgu->getEssence())
                        {
                            $essence = $lignebtgu->getEssence()->getNomVernaculaire();
                        }

                        $my_btgu_page[] = array(
                            'id_ligne'=>$lignebtgu->getId(),
                            'numero_ligne'=>$lignebtgu->getNumeroArbre(),
                            'lettre'=>$lignebtgu->getLettreBille(),
                            'essence'=>$essence,
                            'x'=>$lignebtgu->getX(),
                            'y'=>$lignebtgu->getY(),
                            'zh'=>$zh,
                            'lng'=>$lignebtgu->getLng(),
                            'dm'=>$lignebtgu->getDm(),
                            'volume'=>$lignebtgu->getVolume(),
                            'fini'=>$page_btgu->isFini(),
                            'utilisateur'=>$lignebtgu->getCreatedBy(),
                        );
                    }


                    return  new JsonResponse(json_encode($my_btgu_page));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
}
