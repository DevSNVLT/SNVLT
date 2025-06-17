<?php

namespace App\Controller\DocStats\Entetes;

use App\Controller\Services\AdministrationService;
use App\Controller\Services\synchro\Mouvements\Documents\Documentbrh\DocumentbrhSnvlt1;
use App\Controller\Services\synchro\Mouvements\Documents\Documentcp\DocumentcpSnvlt1;
use App\Entity\Administration\InventaireForestier;
use App\Entity\Autorisation\Attribution;
use App\Entity\Autorisation\Reprise;
use App\Entity\DocStats\Entetes\Documentbrh;
use App\Entity\DocStats\Entetes\Documentcp;
use App\Entity\DocStats\Pages\Pagebrh;
use App\Entity\DocStats\Pages\Pagecp;
use App\Entity\DocStats\Saisie\Lignepagebrh;
use App\Entity\DocStats\Saisie\Lignepagecp;
use App\Entity\References\Cantonnement;
use App\Entity\References\Ddef;
use App\Entity\References\Essence;
use App\Entity\References\Exploitant;
use App\Entity\References\Foret;
use App\Entity\References\Usine;
use App\Entity\References\ZoneHemispherique;
use App\Entity\User;
use App\Repository\Administration\NotificationRepository;
use App\Repository\DocStats\Entetes\DocumentcpRepository;
use App\Repository\DocStats\Pages\PagebrhRepository;
use App\Repository\DocStats\Pages\PagecpRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DocumentcpController extends AbstractController
{
    public function __construct(private ManagerRegistry $m, private AdministrationService $administrationService, private DocumentcpSnvlt1 $snvlt1)
    {
    }

    #[Route('/doc/stats/entetes/documentcp', name: 'app_op_doccp')]
    public function index(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentcpRepository $docs_cp,
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

                return $this->render('doc_stats/entetes/documentcp/index.html.twig', [
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

    #[Route('/snvlt/admin/st_ch', name: 'app_stock_chantier')]
    public function app_stock_chantier(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentcpRepository $docs_cp,
        ManagerRegistry $registry
    ): Response
    {


        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if (
                $this->isGranted('ROLE_EXPLOITANT') or
                $this->isGranted('ROLE_INDUSTRIEL') or
                $this->isGranted('ROLE_MINEF') or
                $this->isGranted('ROLE_ADMIN')  )
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();


                return $this->render('doc_stats/entetes/documentcp/st_chantier.html.twig', [
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

    #[Route('/snvlt/stock/chantier/f/{id_foret}/{critere}', name: 'app_st_chantier')]
    public function stock_chantier(
        Request $request,
        UserRepository $userRepository,
        User $user = null,
        int $id_foret,
        int $critere,
        ManagerRegistry $registry
    ): Response
    {

        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_INDUSTRIEL') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')  )
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $liste_arbres = array();
                //------------------------- Filtre les CC par type Opérateur ------------------------------------- //

                    $foret = $registry->getRepository(Foret::class)->find($id_foret);

                    if ($foret){
                        $attributions = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$foret]);
                      // dd($attributions);
                        foreach ($attributions as $attribution){
                            $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution]);
                            foreach ($reprises as $reprise){
                                $document_cp = $registry->getRepository(Documentcp::class)->findBy(['code_reprise'=>$reprise]);
                                foreach ($document_cp as $doc){
                                    $pagecps = $registry->getRepository(Pagecp::class)->findBy(['code_doccp'=>$doc]);
                                    foreach ($pagecps as $page){
                                        $lignecps = $registry->getRepository(Lignepagecp::class)->findBy(['code_pagecp'=>$page]);
                                        foreach ($lignecps as $lignecp){
                                            $essence = "-";
                                            $zone = "-";
                                            if ($lignecp->getNomEssencecp()){
                                                $essence = $lignecp->getNomEssencecp()->getNomVernaculaire();
                                            }
                                            if ($lignecp->getZhArbrecp()){
                                                $zone = $lignecp->getZhArbrecp()->getZone();
                                            }
                                            if ($critere == 0){
                                                $liste_arbres[] = array(
                                                    'id_ligne'=>$lignecp->getId(),
                                                    'numero_ligne'=>$lignecp->getNumeroArbrecp(),
                                                    'essence'=>$essence,
                                                    'x_arbre'=>$lignecp->getXArbrecp(),
                                                    'y_arbre'=>$lignecp->getYArbrecp(),
                                                    'zh_arbre'=>$zone,
                                                    'jour'=>$lignecp->getJourAbattage(),
                                                    'lng_arbre'=>$lignecp->getLongeurArbrecp(),
                                                    'dm_arbre'=>$lignecp->getDiametreArbrecp(),
                                                    'cubage_arbre'=>round($lignecp->getVolumeArbrecp(), 3),
                                                    'lng_billea'=>$lignecp->getLongeuraBillecp(),
                                                    'dm_billea'=>$lignecp->getDiametreaBillecp(),
                                                    'cubage_billea'=>round($lignecp->getVolumeaBillecp(), 3),
                                                    'lng_billeb'=>$lignecp->getLongeurbBillecp(),
                                                    'dm_billeb'=>$lignecp->getDiametrebBillecp(),
                                                    'cubage_billeb'=>round($lignecp->getVolumebBillecp(), 3),
                                                    'lng_billec'=>$lignecp->getLongeurcBillecp(),
                                                    'dm_billec'=>$lignecp->getDiametrecBillecp(),
                                                    'cubage_billec'=>round($lignecp->getVolumecBillecp(), 3),
                                                    'a_utilise'=>$lignecp->isAUtlise(),
                                                    'b_utilise'=>$lignecp->isBUtilise(),
                                                    'c_utilise'=>$lignecp->isCUtilise(),
                                                    'a_abandon'=>$lignecp->isAAbandon(),
                                                    'b_abandon'=>$lignecp->isBAbandon(),
                                                    'c_abandon'=>$lignecp->isCAbandon(),
                                                    'fut_abandon'=>$lignecp->isFutAbandon(),
                                                    'exploitant'=>$attribution->getCodeExploitant()->getSigle(),
                                                    'cantonnement'=>$foret->getCodeCantonnement()->getNomCantonnement(),
                                                    'dr'=>$foret->getCodeCantonnement()->getCodeDr()->getDenomination()
                                                );
                                            } elseif ($critere == 1){
                                                // Billes sur site
                                                if ($lignecp->getVolumeaBillecp() && !$lignecp->isAUtlise()) {
                                                    $liste_arbres[] = array(
                                                        'id_ligne'=>$lignecp->getId(),
                                                        'numero_ligne'=>$lignecp->getNumeroArbrecp(),
                                                        'bille'=>'A',
                                                        'essence'=>$essence,
                                                        'x_arbre'=>$lignecp->getXArbrecp(),
                                                        'y_arbre'=>$lignecp->getYArbrecp(),
                                                        'zh_arbre'=>$zone,
                                                        'jour'=>$lignecp->getJourAbattage(),
                                                        'lng'=>$lignecp->getLongeuraBillecp(),
                                                        'dm'=>$lignecp->getDiametreaBillecp(),
                                                        'volume'=>round($lignecp->getVolumeaBillecp(), 3)
                                                    );
                                                }elseif ($lignecp->getVolumebBillecp() && !$lignecp->isBUtilise()) {
                                                    $liste_arbres[] = array(
                                                        'id_ligne'=>$lignecp->getId(),
                                                        'numero_ligne'=>$lignecp->getNumeroArbrecp(),
                                                        'bille'=>'B',
                                                        'essence'=>$essence,
                                                        'x_arbre'=>$lignecp->getXArbrecp(),
                                                        'y_arbre'=>$lignecp->getYArbrecp(),
                                                        'zh_arbre'=>$zone,
                                                        'jour'=>$lignecp->getJourAbattage(),
                                                        'lng'=>$lignecp->getLongeurbBillecp(),
                                                        'dm'=>$lignecp->getDiametrebBillecp(),
                                                        'volume'=>round($lignecp->getVolumebBillecp(), 3)
                                                    );
                                                }elseif ($lignecp->getVolumecBillecp() && !$lignecp->isCUtilise()) {
                                                    $liste_arbres[] = array(
                                                        'id_ligne'=>$lignecp->getId(),
                                                        'numero_ligne'=>$lignecp->getNumeroArbrecp(),
                                                        'bille'=>'C',
                                                        'essence'=>$essence,
                                                        'x_arbre'=>$lignecp->getXArbrecp(),
                                                        'y_arbre'=>$lignecp->getYArbrecp(),
                                                        'zh_arbre'=>$zone,
                                                        'jour'=>$lignecp->getJourAbattage(),
                                                        'lng'=>$lignecp->getLongeurcBillecp(),
                                                        'dm'=>$lignecp->getDiametrecBillecp(),
                                                        'cubage'=>round($lignecp->getVolumecBillecp(), 3)
                                                    );
                                                }

                                            } elseif ($critere == 2){
                                                // Recherche de billes abandonnées
                                                if ($lignecp->getVolumeaBillecp() && $lignecp->isAAbandon()) {
                                                    $liste_arbres[] = array(
                                                        'id_ligne'=>$lignecp->getId(),
                                                        'numero_ligne'=>$lignecp->getNumeroArbrecp(),
                                                        'bille'=>'A',
                                                        'essence'=>$essence,
                                                        'x_arbre'=>$lignecp->getXArbrecp(),
                                                        'y_arbre'=>$lignecp->getYArbrecp(),
                                                        'zh_arbre'=>$zone,
                                                        'jour'=>$lignecp->getJourAbattage(),
                                                        'lng'=>$lignecp->getLongeuraBillecp(),
                                                        'dm'=>$lignecp->getDiametreaBillecp(),
                                                        'volume'=>round($lignecp->getVolumeaBillecp(), 3)
                                                    );
                                                } elseif ($lignecp->getVolumebBillecp() && $lignecp->isBAbandon()) {
                                                    $liste_arbres[] = array(
                                                        'id_ligne'=>$lignecp->getId(),
                                                        'numero_ligne'=>$lignecp->getNumeroArbrecp(),
                                                        'bille'=>'B',
                                                        'essence'=>$essence,
                                                        'x_arbre'=>$lignecp->getXArbrecp(),
                                                        'y_arbre'=>$lignecp->getYArbrecp(),
                                                        'zh_arbre'=>$zone,
                                                        'jour'=>$lignecp->getJourAbattage(),
                                                        'lng'=>$lignecp->getLongeurbBillecp(),
                                                        'dm'=>$lignecp->getDiametrebBillecp(),
                                                        'volume'=>round($lignecp->getVolumebBillecp(), 3)
                                                    );
                                                }elseif ($lignecp->getVolumecBillecp() && $lignecp->isCAbandon()) {
                                                    $liste_arbres[] = array(
                                                        'id_ligne'=>$lignecp->getId(),
                                                        'numero_ligne'=>$lignecp->getNumeroArbrecp(),
                                                        'bille'=>'C',
                                                        'essence'=>$essence,
                                                        'x_arbre'=>$lignecp->getXArbrecp(),
                                                        'y_arbre'=>$lignecp->getYArbrecp(),
                                                        'zh_arbre'=>$zone,
                                                        'jour'=>$lignecp->getJourAbattage(),
                                                        'lng'=>$lignecp->getLongeurcBillecp(),
                                                        'dm'=>$lignecp->getDiametrecBillecp(),
                                                        'volume'=>round($lignecp->getVolumecBillecp(), 3)
                                                    );
                                                }elseif ($lignecp->getVolumeArbrecp() && $lignecp->isFutAbandon()) {
                                                    $liste_arbres[] = array(
                                                        'id_ligne'=>$lignecp->getId(),
                                                        'numero_ligne'=>$lignecp->getNumeroArbrecp(),
                                                        'bille'=>'Fût',
                                                        'essence'=>$essence,
                                                        'x_arbre'=>$lignecp->getXArbrecp(),
                                                        'y_arbre'=>$lignecp->getYArbrecp(),
                                                        'zh_arbre'=>$zone,
                                                        'jour'=>$lignecp->getJourAbattage(),
                                                        'lng'=>$lignecp->getLongeurArbrecp(),
                                                        'dm'=>$lignecp->getDiametreArbrecp(),
                                                        'volume'=>round($lignecp->getVolumeArbrecp(), 3)
                                                    );
                                                }
                                            } elseif ($critere == 3){
                                                // Recherche de Billes utilisées
                                                if ($lignecp->getVolumeaBillecp() && $lignecp->isAUtlise()) {
                                                    $liste_arbres[] = array(
                                                        'id_ligne'=>$lignecp->getId(),
                                                        'numero_ligne'=>$lignecp->getNumeroArbrecp(),
                                                        'bille'=>'A',
                                                        'essence'=>$essence,
                                                        'x_arbre'=>$lignecp->getXArbrecp(),
                                                        'y_arbre'=>$lignecp->getYArbrecp(),
                                                        'zh_arbre'=>$zone,
                                                        'jour'=>$lignecp->getJourAbattage(),
                                                        'lng'=>$lignecp->getLongeuraBillecp(),
                                                        'dm'=>$lignecp->getDiametreaBillecp(),
                                                        'volume'=>round($lignecp->getVolumeaBillecp(), 3)
                                                    );
                                                }
                                                if ($lignecp->getVolumebBillecp() && $lignecp->isBUtilise()) {
                                                    $liste_arbres[] = array(
                                                        'id_ligne'=>$lignecp->getId(),
                                                        'numero_ligne'=>$lignecp->getNumeroArbrecp(),
                                                        'bille'=>'B',
                                                        'essence'=>$essence,
                                                        'x_arbre'=>$lignecp->getXArbrecp(),
                                                        'y_arbre'=>$lignecp->getYArbrecp(),
                                                        'zh_arbre'=>$zone,
                                                        'jour'=>$lignecp->getJourAbattage(),
                                                        'lng'=>$lignecp->getLongeurbBillecp(),
                                                        'dm'=>$lignecp->getDiametrebBillecp(),
                                                        'volume'=>round($lignecp->getVolumebBillecp(), 3)
                                                    );
                                                }
                                                if ($lignecp->getVolumecBillecp() && $lignecp->isCUtilise()) {
                                                    $liste_arbres[] = array(
                                                        'id_ligne'=>$lignecp->getId(),
                                                        'numero_ligne'=>$lignecp->getNumeroArbrecp(),
                                                        'bille'=>'C',
                                                        'essence'=>$essence,
                                                        'x_arbre'=>$lignecp->getXArbrecp(),
                                                        'y_arbre'=>$lignecp->getYArbrecp(),
                                                        'zh_arbre'=>$zone,
                                                        'jour'=>$lignecp->getJourAbattage(),
                                                        'lng'=>$lignecp->getLongeurcBillecp(),
                                                        'dm'=>$lignecp->getDiametrecBillecp(),
                                                        'cubage'=>round($lignecp->getVolumecBillecp(), 3)
                                                    );
                                                }

                                            }

                                        }
                                    }
                                }
                            }
                        }
                    }
                   return  new JsonResponse(json_encode($liste_arbres));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/doccp/op', name: 'app_docs_cp_json')]
    public function my_doc_cp(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentcpRepository $docs_cp,
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

                $mes_docs_cp = array();
                //------------------------- Filtre les CC par type Opérateur ------------------------------------- //

                //------------------------- Filtre les CC ADMIN ------------------------------------- //
                if($user->getCodeGroupe()->getId() == 1  or $this->isGranted('ROLE_DPIF_SAISIE')){
                            $documents_cp = $registry->getRepository(Documentcp::class)->findBy(['exercice'=>$this->administrationService->getAnnee()]);
                            foreach ($documents_cp as $document_cp){


                                if ($document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()){
                                    $canton = $document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getNomCantonnement();
                                    $d = $document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getCodeDr()->getDenomination();
                                }else {
                                    $canton = "-";
                                    $d = "-";
                                }


                                $nb_arbres = 0;
                                $pages = $registry->getRepository(Pagecp::class)->findBy(['code_doccp'=>$document_cp]);

                                foreach($pages as $page) {
                                    $lignes = $registry->getRepository(Lignepagecp::class)->findBy(['code_pagecp' => $page]);
                                    foreach ($lignes as $ligne) {
                                        $nb_arbres = $nb_arbres + 1;
                                    }
                                }

                                $mes_docs_cp[] = array(
                                    'id_document_cp'=>$document_cp->getId(),
                                    'numero_doccp'=>$document_cp->getNumeroDoccp(),
                                    'foret'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination(),
                                    'cantonnement'=>$canton,
                                    'dr'=>$d,
                                    'date_delivrance'=>$document_cp->getDelivreDoccp()->format("d m Y"),
                                    'etat'=>$document_cp->isEtat(),
                                    'exploitant'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getRaisonSocialeExploitant(),
                                    'code_exploitant'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getNumeroExploitant(),
                                    'volume_arbre'=>round($this->getVolumeCp($document_cp), 3),
                                    'nb_arbres'=>$nb_arbres
                                );
                            }
                        //------------------------- Filtre les CC DR ------------------------------------- //
                        } else {
                            if ($user->getCodeDr()){
                                //dd($user->getCodeDr());
                                $cantonnements = $registry->getRepository(Cantonnement::class)->findBy(['code_dr'=>$user->getCodeDr()]);
                                foreach ($cantonnements as $cantonnement){
                                    $forets = $registry->getRepository(Foret::class)->findBy(['code_cantonnement'=>$cantonnement]);

                                    foreach ($forets as $foret){
                                        $attributions = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$foret, 'statut'=>true, 'reprise'=>true, 'exercice'=>$this->administrationService->getAnnee()]);
                                        foreach ($attributions as $attribution){
                                            $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution, 'statut'=>true]);
                                            foreach ($reprises as $reprise){
                                                $documents_cp = $registry->getRepository(Documentcp::class)->findBy(['code_reprise'=>$reprise]);
                                                foreach ($documents_cp as $document_cp){


                                                    if ($document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()){
                                                        $canton = $document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getNomCantonnement();
                                                        $d = $document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getCodeDr()->getDenomination();
                                                    }else {
                                                        $canton = "-";
                                                        $d = "-";
                                                    }


                                                    $nb_arbres = 0;
                                                    $pages = $registry->getRepository(Pagecp::class)->findBy(['code_doccp'=>$document_cp]);

                                                    foreach($pages as $page) {
                                                        $lignes = $registry->getRepository(Lignepagecp::class)->findBy(['code_pagecp' => $page]);
                                                        foreach ($lignes as $ligne) {
                                                            $nb_arbres = $nb_arbres + 1;
                                                        }
                                                    }


                                                    $mes_docs_cp[] = array(
                                                        'id_document_cp'=>$document_cp->getId(),
                                                        'numero_doccp'=>$document_cp->getNumeroDoccp(),
                                                        'foret'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination(),
                                                        'cantonnement'=>$canton,
                                                        'dr'=>$d,
                                                        'date_delivrance'=>$document_cp->getDelivreDoccp()->format("d m Y"),
                                                        'etat'=>$document_cp->isEtat(),
                                                        'attribution_attribue'=>$document_cp->getCodeReprise()->getCodeAttribution()->isStatut(),
                                                        'reprise_attribue'=>$document_cp->getCodeReprise()->getCodeAttribution()->isReprise(),
                                                        'exploitant'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getRaisonSocialeExploitant(),
                                                        'code_exploitant'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getNumeroExploitant(),
                                                        'volume_arbre'=>round($this->getVolumeCp($document_cp), 3),
                                                        'nb_arbres'=>$nb_arbres
                                                    );
                                                }

                                            }
                                        }
                                    }
                                }

                            //------------------------- Filtre les CC DD ------------------------------------- //
                        } elseif ($user->getCodeDdef()){
                                        $cantonnements = $registry->getRepository(Cantonnement::class)->findBy(['code_ddef'=>$user->getCodeDdef()]);
                                        foreach ($cantonnements as $cantonnement){
                                            $forets = $registry->getRepository(Foret::class)->findBy(['code_cantonnement'=>$cantonnement]);
                                            foreach ($forets as $foret){
                                                $attributions = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$user->getCodeexploitant(), 'statut'=>true, 'reprise'=>true, 'exercice'=>$this->administrationService->getAnnee()]);
                                                foreach ($attributions as $attribution){
                                                    $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution, 'statut'=>true]);
                                                    foreach ($reprises as $reprise){
                                                        $documents_cp = $registry->getRepository(Documentcp::class)->findBy(['code_reprise'=>$reprise]);
                                                        foreach ($documents_cp as $document_cp){


                                                            if ($document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()){
                                                                $canton = $document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getNomCantonnement();
                                                                $d = $document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getCodeDr()->getDenomination();
                                                            }else {
                                                                $canton = "-";
                                                                $d = "-";
                                                            }


                                                            $nb_arbres = 0;
                                                            $pages = $registry->getRepository(Pagecp::class)->findBy(['code_doccp'=>$document_cp]);

                                                            foreach($pages as $page) {
                                                                $lignes = $registry->getRepository(Lignepagecp::class)->findBy(['code_pagecp' => $page]);
                                                                foreach ($lignes as $ligne) {
                                                                    $nb_arbres = $nb_arbres + 1;
                                                                }
                                                            }


                                                            $mes_docs_cp[] = array(
                                                                'id_document_cp'=>$document_cp->getId(),
                                                                'numero_doccp'=>$document_cp->getNumeroDoccp(),
                                                                'foret'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination(),
                                                                'cantonnement'=>$canton,
                                                                'dr'=>$d,
                                                                'date_delivrance'=>$document_cp->getDelivreDoccp()->format("d m Y"),
                                                                'etat'=>$document_cp->isEtat(),
                                                                'attribution_attribue'=>$document_cp->getCodeReprise()->getCodeAttribution()->isStatut(),
                                                                'reprise_attribue'=>$document_cp->getCodeReprise()->getCodeAttribution()->isReprise(),
                                                                'exploitant'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getRaisonSocialeExploitant(),
                                                                'code_exploitant'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getNumeroExploitant(),
                                                                'volume_arbre'=>round($this->getVolumeCp($document_cp), 3),
                                                                'nb_arbres'=>$nb_arbres
                                                            );
                                                        }

                                                    }
                                                }
                                            }
                                        }

                    //------------------------- Filtre les CC CANTONNEMENT ------------------------------------- //
                        } elseif ($user->getCodeCantonnement()){
                                    $forets = $registry->getRepository(Foret::class)->findBy(['code_cantonnement'=>$user->getCodeCantonnement()]);

                                    foreach ($forets as $foret){
                                        $attributions = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$foret, 'statut'=>true, 'reprise'=>true, 'exercice'=>$this->administrationService->getAnnee()]);
                                        foreach ($attributions as $attribution){
                                            $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution, 'statut'=>true]);
                                            foreach ($reprises as $reprise){
                                                $documents_cp = $registry->getRepository(Documentcp::class)->findBy(['code_reprise'=>$reprise]);
                                                foreach ($documents_cp as $document_cp){


                                                    if ($document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()){
                                                        $canton = $document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getNomCantonnement();
                                                        $d = $document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getCodeDr()->getDenomination();
                                                    }else {
                                                        $canton = "-";
                                                        $d = "-";
                                                    }


                                                    $nb_arbres = 0;
                                                    $pages = $registry->getRepository(Pagecp::class)->findBy(['code_doccp'=>$document_cp]);

                                                    foreach($pages as $page) {
                                                        $lignes = $registry->getRepository(Lignepagecp::class)->findBy(['code_pagecp' => $page]);
                                                        foreach ($lignes as $ligne) {
                                                            $nb_arbres = $nb_arbres + 1;
                                                        }
                                                    }



                                                    $mes_docs_cp[] = array(
                                                        'id_document_cp'=>$document_cp->getId(),
                                                        'numero_doccp'=>$document_cp->getNumeroDoccp(),
                                                        'foret'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination(),
                                                        'cantonnement'=>$canton,
                                                        'dr'=>$d,
                                                        'date_delivrance'=>$document_cp->getDelivreDoccp()->format("d m Y"),
                                                        'etat'=>$document_cp->isEtat(),
                                                        'attribution_attribue'=>$document_cp->getCodeReprise()->getCodeAttribution()->isStatut(),
                                                        'reprise_attribue'=>$document_cp->getCodeReprise()->getCodeAttribution()->isReprise(),
                                                        'exploitant'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getRaisonSocialeExploitant(),
                                                        'code_exploitant'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getNumeroExploitant(),
                                                        'volume_arbre'=>round($this->getVolumeCp($document_cp), 3),
                                                        'nb_arbres'=>$nb_arbres
                                                    );
                                                }

                                            }
                                        }
                                    }

                    //------------------------- Filtre les CC POSTE CONTROLE ------------------------------------- //
                  } elseif ($user->getCodePosteControle()){
                    $forets = $registry->getRepository(Foret::class)->findBy(['code_cantonnement'=>$user->getCodePosteControle()->getCodeCantonnement()]);
                    foreach ($forets as $foret){
                        $attributions = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$user->getCodeexploitant(), 'statut'=>true, 'reprise'=>true, 'exercice'=>$this->administrationService->getAnnee()]);
                        foreach ($attributions as $attribution){
                            $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution, 'statut'=>true]);
                            foreach ($reprises as $reprise){
                                $documents_cp = $registry->getRepository(Documentcp::class)->findBy(['code_reprise'=>$reprise]);
                                foreach ($documents_cp as $document_cp){


                                    if ($document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()){
                                        $canton = $document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getNomCantonnement();
                                        $d = $document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getCodeDr()->getDenomination();
                                    }else {
                                        $canton = "-";
                                        $d = "-";
                                    }


                                    $nb_arbres = 0;
                                    $pages = $registry->getRepository(Pagecp::class)->findBy(['code_doccp'=>$document_cp]);

                                    foreach($pages as $page) {
                                        $lignes = $registry->getRepository(Lignepagecp::class)->findBy(['code_pagecp' => $page]);
                                        foreach ($lignes as $ligne) {
                                            $nb_arbres = $nb_arbres + 1;
                                        }
                                    }



                                    $mes_docs_cp[] = array(
                                        'id_document_cp'=>$document_cp->getId(),
                                        'numero_doccp'=>$document_cp->getNumeroDoccp(),
                                        'foret'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination(),
                                        'cantonnement'=>$canton,
                                        'dr'=>$d,
                                        'date_delivrance'=>$document_cp->getDelivreDoccp()->format("d m Y"),
                                        'etat'=>$document_cp->isEtat(),
                                        'attribution_attribue'=>$document_cp->getCodeReprise()->getCodeAttribution()->isStatut(),
                                        'reprise_attribue'=>$document_cp->getCodeReprise()->getCodeAttribution()->isReprise(),
                                        'exploitant'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getRaisonSocialeExploitant(),
                                        'code_exploitant'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getNumeroExploitant(),
                                        'volume_arbre'=>round($this->getVolumeCp($document_cp), 3),
                                        'nb_arbres'=>$nb_arbres
                                    );
                                }

                            }
                        }
                    }
                //------------------------- Filtre les CC EXPLOITANT------------------------------------- //
                } elseif ($user->getCodeexploitant()){
                    $attributions = $registry->getRepository(Attribution::class)->findBy(['code_exploitant'=>$user->getCodeexploitant(), 'statut'=>true, 'reprise'=>true, 'exercice'=>$this->administrationService->getAnnee()]);
                    foreach ($attributions as $attribution){
                        $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution, 'statut'=>true]);

                        foreach ($reprises as $reprise){
                            $documents_cp = $registry->getRepository(Documentcp::class)->findBy(['code_reprise'=>$reprise, 'signature_cef'=>true, 'signature_dr'=>true],['created_at'=>'DESC']);

                            foreach ($documents_cp as $document_cp){
                                $nb_arbres = 0;
                                $pages = $registry->getRepository(Pagecp::class)->findBy(['code_doccp'=>$document_cp]);


                                if ($document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()){
                                    $canton = $document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getNomCantonnement();
                                    $d = $document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getCodeDr()->getDenomination();
                                }else {
                                    $canton = "-";
                                    $d = "-";
                                }


                                foreach($pages as $page){
                                    $lignes = $registry->getRepository(Lignepagecp::class)->findBy(['code_pagecp'=>$page]);
                                    foreach ($lignes as $ligne){
                                        $nb_arbres = $nb_arbres + 1;
                                    }

                                }
                                $mes_docs_cp[] = array(
                                    'id_document_cp'=>$document_cp->getId(),
                                    'numero_doccp'=>$document_cp->getNumeroDoccp(),
                                    'foret'=>$document_cp->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination(),
                                    'cantonnement'=>$canton,
                                    'dr'=>$d,
                                    'date_delivrance'=>$document_cp->getDelivreDoccp()->format("d m Y"),
                                    'etat'=>$document_cp->isEtat(),
                                    'volume_arbre'=>round($this->getVolumeCp($document_cp), 3),
                                    'nb_arbres'=>$nb_arbres
                                );
                            }

                        }

                    }
                }


                }
                return new JsonResponse(json_encode($mes_docs_cp));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }

    }

    #[Route('/snvlt/doccp/op/pages/{id_cp}', name: 'affichage_cp_json')]
    public function affiche_cp(
        Request $request,
        int $id_cp,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentcpRepository $docs_cp,
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

                $numerodoc = "";

                $documentcp = $registry->getRepository(Documentcp::class)->find($id_cp);
                if($documentcp){$numerodoc = $documentcp->getNumeroDoccp();}

                return $this->render('doc_stats/entetes/documentcp/affiche_my_cp.html.twig', [
                    'document_name'=>$documentcp,
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'groupe'=>$code_groupe,
                    'liste_parent'=>$permissions,
                    'exercice'=>$this->administrationService->getAnnee()->getAnnee()
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/doccp/op/pages_cp/{id_cp}', name: 'affichage_pages_cp_json')]
    public function affiche_pages_cp(
        Request $request,
        int $id_cp,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentcpRepository $docs_cp,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')  )
            {
                $doc_cp = $docs_cp->find($id_cp);
                if($doc_cp){
                    $pages_cp = $registry->getRepository(Pagecp::class)->findBy(['code_doccp'=>$doc_cp], ['id'=>'ASC']);
                    $my_cp_pages = array();

                    foreach ($pages_cp as $page){
                        $my_cp_pages[] = array(
                            'id_page'=>$page->getId(),
                            'numero_page'=>$page->getNumeroPagecp()
                        );
                    }
                    return  new JsonResponse(json_encode($my_cp_pages));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/doccp/op/pages_cp/data/{id_page}', name: 'affichage_page_data_cp_json')]
    public function affiche_page_courante(
        Request $request,
        int $id_page,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        PagecpRepository $pages_cp,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')  )
            {
                $page_cp = $pages_cp->find($id_page);
                if($page_cp){
                    $my_cp_page = array();
                        $my_cp_page[] = array(
                            'id_page'=>$page_cp->getId(),
                            'numero_page'=>$page_cp->getNumeroPagecp(),
                            'mois'=>$page_cp->getMois(),
                            'annee'=>$page_cp->getAnnee(),
                            'village'=>$page_cp->getVillagePagecp()
                        );

                    return  new JsonResponse(json_encode($my_cp_page));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/doccp/op/lignes_cp/data/{id_page}', name: 'affichage_ligne_cp_data_cp_json')]
    public function affiche_lignes_cp_courante(
        Request $request,
        int $id_page,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        PagecpRepository $pages_cp,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')  )
            {
                $page_cp = $pages_cp->find($id_page);
                if($page_cp){
                    $lignes_cp = $registry->getRepository(Lignepagecp::class)->findBy(['code_pagecp'=>$page_cp]);
                    $my_cp_page = array();
                    foreach ($lignes_cp as $lignecp){
                        $essence = '-';
                        $zone = '-';

                        if ($lignecp->getNomEssencecp()) { $essence = $lignecp->getNomEssencecp()->getNomVernaculaire();}
                        if ($lignecp->getZhArbrecp()) { $zone = $lignecp->getZhArbrecp()->getZone();}

                        $my_cp_page[] = array(
                            'id_ligne'=>$lignecp->getId(),
                            'numero_ligne'=>$lignecp->getNumeroArbrecp(),
                            'essence'=>$essence,
                            'x_arbre'=>$lignecp->getXArbrecp(),
                            'y_arbre'=>$lignecp->getYArbrecp(),
                            'zh_arbre'=>$zone,
                            'jour'=>$lignecp->getJourAbattage(),
                            'lng_arbre'=>$lignecp->getLongeurArbrecp(),
                            'dm_arbre'=>$lignecp->getDiametreArbrecp(),
                            'cubage_arbre'=>$lignecp->getVolumeArbrecp(),
                            'lng_billea'=>$lignecp->getLongeuraBillecp(),
                            'dm_billea'=>$lignecp->getDiametreaBillecp(),
                            'cubage_billea'=>$lignecp->getVolumeaBillecp(),
                            'lng_billeb'=>$lignecp->getLongeurbBillecp(),
                            'dm_billeb'=>$lignecp->getDiametrebBillecp(),
                            'cubage_billeb'=>$lignecp->getVolumebBillecp(),
                            'lng_billec'=>$lignecp->getLongeurcBillecp(),
                            'dm_billec'=>$lignecp->getDiametrecBillecp(),
                            'cubage_billec'=>$lignecp->getVolumecBillecp(),
                            'a_abandon'=>$lignecp->isAAbandon(),
                            'b_abandon'=>$lignecp->isBAbandon(),
                            'c_abandon'=>$lignecp->isCAbandon(),
                            'fut_abandon'=>$lignecp->isFutAbandon(),
                            'charge'=>$lignecp->isCharge()
                        );
                    }


                    return  new JsonResponse(json_encode($my_cp_page));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
    #[Route('snvlt/doccp/deplacer/pages_cp/{id_arbre}/{id_page}', name: 'deplacer_fut')]
    public function deplacer_fut(
        Request $request,
        int $id_arbre,
        int $id_page,
        UserRepository $userRepository,
        PagecpRepository $pages_cp,
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

                $page_cp = $pages_cp->find($id_page);
                $ligne_cp = $registry->getRepository(Lignepagecp::class)->find($id_arbre);
                if ($ligne_cp and $page_cp){
                    if ($page_cp->getAnnee() and $page_cp->getMois()){
                        $ligne_cp->setUpdatedAt(new \DateTime());
                        $ligne_cp->setUpdatedBy($user);
                        $ligne_cp->setCodePagecp($page_cp);

                        $registry->getManager()->persist($ligne_cp);
                        $registry->getManager()->flush();

                        //Mise à jour dans SNVLT1
                        $this->snvlt1->EditLignePageDoccp($ligne_cp);

                        $my_cp_page[] = array(
                            'code'=>'SUCCESS'
                        );
                    } else {
                        $my_cp_page[] = array(
                            'code'=>'PAGE_NON_RENSEIGNEE'
                        );
                    }

                } else {
                    $my_cp_page[] = array(
                        'code'=>'ERROR'
                    );
                }
                return  new JsonResponse(json_encode($my_cp_page));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/doccp/retirer/pages_cp/{id_arbre}', name: 'retirer_fut')]
    public function retirer_fut(
        Request $request,
        int $id_arbre,
        UserRepository $userRepository,
        PagecpRepository $pages_cp,
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

                $ligne_cp = $registry->getRepository(Lignepagecp::class)->find($id_arbre);
                if ($ligne_cp){
                    //recherche du ID CP dans la Table BRH
                    $ligne_brh = $registry->getRepository(Lignepagebrh::class)->findOneBy(['code_ligne_cp'=>$ligne_cp]);
                    if (!$ligne_brh){
                        $registry->getManager()->remove($ligne_cp);
                        $registry->getManager()->flush();

                        //Mise à jour dans SNVLT1
                        try {
                            $this->snvlt1->DeleteLignePageDoccp($ligne_cp);
                        } catch (\Throwable $exception){
                            $my_cp_page[] = array(
                                'code'=>$exception->getMessage()
                            );
                        }


                        $my_cp_page[] = array(
                            'code'=>'SUCCESS'
                        );
                    } else {
                        $my_cp_page[] = array(
                            'code'=>'ERROR'
                        );
                    }

                } else {
                    $my_cp_page[] = array(
                        'code'=>'ERROR'
                    );
                }
                return  new JsonResponse(json_encode($my_cp_page));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    function getVolumeCp(Documentcp $documentcp):float
    {
        $volumecp = 0;
        if($documentcp){
            $pagecp =$this->m->getRepository(Pagecp::class)->findBy(['code_doccp'=>$documentcp]);
            foreach ($pagecp as $page){
                $lignepages = $this->m->getRepository(Lignepagecp::class)->findBy(['code_pagecp'=>$page]);
                foreach ($lignepages as $ligne){
                   $volumecp = $volumecp +  $ligne->getVolumeArbrecp();
                }
            }
            return $volumecp;
        } else {
            return $volumecp;
        }
    }


    #[Route('/snvlt/doccp/op/pages_cp/data/edit/{id_page}/{data}', name: 'edit_page_cp_json')]
    public function edit_page_cp(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        string $data,
        int $id_page,
        NotificationRepository $notification,
        PagecpRepository $pages_cp,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_DPIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                //$page_cp = $pages_cp->find($id_page);
                if($data){
                    $pagecp = $registry->getRepository(Pagecp::class)->find($id_page);

                    if ($pagecp){
                        //Decoder le JSON BRH
                        $arraydata = json_decode($data);

                        //dd($arraydata->numero_lignepagecp);
                        $date_jour = new \DateTime();
                        $pagecp->setVillagePagecp(strtoupper($arraydata->village));
                        $pagecp->setMois((int) $arraydata->mois);
                        $pagecp->setAnnee((int) $arraydata->annee);
                        $pagecp->setUpdatedAt(new \DateTime());
                        $pagecp->setUpdatedBy($user);

                        $registry->getManager()->persist($pagecp);
                        $registry->getManager()->flush();

                        // Mise à jour dans SNVLT1
                           $this->snvlt1->EditPageDoccp($pagecp);
                        return  new JsonResponse([
                            'code'=>'PAGE_CC_EDITED_SUCCESSFULLY'
                        ]);
                    }

                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/foret/dn/{id_page}', name: 'dernier_numero')]
    public function dernier_numero(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_page,
        NotificationRepository $notification,
        PagecpRepository $pages_cp,
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

                //$page_cp = $pages_cp->find($id_page);

                    $pagecp = $registry->getRepository(Pagecp::class)->find($id_page);
                    $foret = $pagecp->getCodeDoccp()->getCodeReprise()->getCodeAttribution()->getCodeForet();
                    $numero = array();

                    if ($foret) {

                        if ($foret->getDernierNumero()) {
                            $numero[] = array(
                                'numero'=> $foret->getDernierNumero()
                            );
                        } else {
                            $numero[] = array(
                                'numero'=> 0
                            );
                        }
                    }


                        return  new JsonResponse(json_encode($numero));
                    } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/doccp/op/pages_cp/data/add_lignes/{data}/{id_foret}', name: 'adddata_cp_json')]
    public function add_lignes_cp(
        Request $request,
        UserRepository $userRepository,
        string $data,
        int $id_foret,
        NotificationRepository $notification,
        PagecpRepository $pages_cp,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_DPIF')  or $this->isGranted('ROLE_DPIF_SAISIE'))
            {
                $user = $userRepository->find($this->getUser());

                if($data){
                    //Decoder le JSON BRH
                    $arraydata = json_decode($data);

                    $isSameValue = false;

                    //Recherche la foret
                    $mes_attributions = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$registry->getRepository(Foret::class)->find($id_foret)]);
                    foreach($mes_attributions as $attribution){
                        $mes_reprises =$registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution]);
                        foreach($mes_reprises as $reprise){
                            $mes_cp = $registry->getRepository(Documentcp::class)->findBy(['code_reprise'=>$reprise]);
                            foreach($mes_cp as $cp){
                                $mes_pages = $registry->getRepository(Pagecp::class)->findBy(['code_doccp'=>$cp]);
                                foreach($mes_pages as $pagecp){
                                    $mes_lignes = $registry->getRepository(Lignepagecp::class)->findBy(['code_pagecp'=>$pagecp]);
                                    foreach($mes_lignes as $ligne){
                                        if ($ligne->getNumeroArbrecp() == (int) $arraydata->numero_arbrecp
                                        ){
                                            $isSameValue = true;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $essence =  $registry->getRepository(Essence::class)->find((int)  $arraydata->nom_essencecp);
                    $zone = $registry->getRepository(ZoneHemispherique::class)->find((int) $arraydata->zh_arbrecp);
                    //dd($zone);
                    if(!$isSameValue){
                        // L'arbre n'a pas de numéro dupliqué
                        if ($essence and $zone) {
                            $lignecp = new Lignepagecp();

                            $date_jour = new \DateTime();
                            $lignecp->setNumeroArbrecp((int) $arraydata->numero_arbrecp);
                            $lignecp->setNomEssencecp($essence);
                            $lignecp->setZhArbrecp($zone);
                            $lignecp->setJourAbattage($arraydata->jour_abattage);
                            $lignecp->setXArbrecp((float) $arraydata->x_arbrecp);
                            $lignecp->setYArbrecp((float)$arraydata->y_arbrecp);
                            $lignecp->setLongeurArbrecp((int) $arraydata->longeur_arbrecp);
                            $lignecp->setDiametreArbrecp((int) $arraydata->diametre_arbrecp);
                            $lignecp->setVolumeArbrecp((float)$arraydata->volume_arbrecp);

                            $lignecp->setLongeuraBillecp((int) $arraydata->longeura_billecp);
                            $lignecp->setDiametreaBillecp((int) $arraydata->diametrea_billecp);
                            $lignecp->setVolumeaBillecp((float)$arraydata->volumea_billecp);

                            $lignecp->setLongeurbBillecp((int) $arraydata->longeurb_billecp);
                            $lignecp->setDiametrebBillecp((int) $arraydata->diametreb_billecp);
                            $lignecp->setVolumebBillecp((float)$arraydata->volumeb_billecp);

                            $lignecp->setLongeurcBillecp((int) $arraydata->longeurc_billecp);
                            $lignecp->setDiametrecBillecp((int) $arraydata->diametrec_billecp);
                            $lignecp->setVolumecBillecp((float)$arraydata->volumec_billecp);


                            $lignecp->setAUtlise(false);

                            if ($arraydata->longeurb_billecp){
                                $lignecp->setBUtilise(false);
                            } else {
                                $lignecp->setBUtilise(true);
                            }

                            if ($arraydata->longeurc_billecp){
                                $lignecp->setCUtilise(false);
                            } else {
                                $lignecp->setCUtilise(true);
                            }


                            /*Vérifie si le Fût est abandonné*/
                            if ($arraydata->abandon_fut == "true"){
                                $lignecp->setFutAbandon(true);
                            } else {
                                $lignecp->setFutAbandon(false);
                            }

                            /*Vérifie si la bille A est abandonnée*/
                            if ($arraydata->abandon_a == "true"){
                                $lignecp->setAAbandon(true);
                                $lignecp->setAUtlise(true);
                            } else {
                                $lignecp->setAAbandon(false);
                            }

                            /*Vérifie si la bille B est abandonnée*/
                            if ($arraydata->abandon_b == "true"){
                                $lignecp->setBAbandon(true);
                                $lignecp->setBUtilise(true);
                            } else {
                                $lignecp->setBAbandon(false);
                            }

                            /*Vérifie si la bille C est abandonnée*/
                            if ($arraydata->abandon_c == "true"){
                                $lignecp->setCAbandon(true);
                                $lignecp->setCUtilise(true);
                            } else {
                                $lignecp->setCAbandon(false);
                            }


                            $lignecp->setCreatedAt($date_jour);
                            $lignecp->setCreatedBy($user);
                            $lignecp->setCodePagecp($registry->getRepository(Pagecp::class)->find((int) $arraydata->code_pagecp));


                            //Vérifie si la bille provient de l'inventaire et l'ajoute
                            if ($arraydata->id_inv){
                                //recherche la ligne correspondant à l'arbre inventorié
                                $arbre_inv = $registry->getRepository(InventaireForestier::class)->find((int) $arraydata->id_inv);
                                //dd($arbre_inv);
                                if($arbre_inv){
                                    $lignecp->setCodeInv($arbre_inv);
                                }
                            }

                            $registry->getManager()->persist($lignecp);

                            // Mise à jour du numero CC de la foret
                            $registry->getRepository(Foret::class)->find($id_foret)->setDernierNumero($lignecp->getNumeroArbrecp());

                            $registry->getManager()->flush();

                            //Ajout de la donnée dans SNVLT
                            $this->snvlt1->AddLignePageDoccp($lignecp);

                            $response = array();
                            $response[] = array(
                                'code_cp'=>'LIGNE_CC_ADDED_SUCCESSFULLY',
                                'exo'=>$this->administrationService->getAnnee()->getAnnee()
                            );
                        } else {
                            $response[] = array(
                                'code_cp'=>'LIGNE_CC_NO_SPECIES_OR_ZONE',
                                'exo'=>$this->administrationService->getAnnee()->getAnnee()
                            );
                        }


                    } else {
                        $response[] = array(
                            'code_cp'=>'SAME_NUMBER',
                            'exo'=>$this->administrationService->getAnnee()->getAnnee()
                        );

                    }
                    return  new JsonResponse(json_encode($response));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
#[Route('/snvlt/doccp/op/pages_cp/data/add_lignes/db/{data}/{id_foret}', name: 'adddata_cp_db_json')]
    public function add_lignes_cp_db(
        Request $request,
        UserRepository $userRepository,
        string $data,
        int $id_foret,
        NotificationRepository $notification,
        PagecpRepository $pages_cp,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_DPIF')  or $this->isGranted('ROLE_DPIF_SAISIE'))
            {
                $user = $userRepository->find($this->getUser());

                if($data){
                    //Decoder le JSON BRH
                    $arraydata = json_decode($data);


                    $essence =  $registry->getRepository(Essence::class)->find((int)  $arraydata->nom_essencecp);
                    $zone = $registry->getRepository(ZoneHemispherique::class)->find((int) $arraydata->zh_arbrecp);

                        if ($essence and $zone) {
                            $lignecp = new Lignepagecp();

                            $date_jour = new \DateTime();
                            $lignecp->setNumeroArbrecp((int) $arraydata->numero_arbrecp);
                            $lignecp->setNomEssencecp($essence);
                            $lignecp->setZhArbrecp($zone);
                            $lignecp->setJourAbattage($arraydata->jour_abattage);
                            $lignecp->setXArbrecp((float) $arraydata->x_arbrecp);
                            $lignecp->setYArbrecp((float)$arraydata->y_arbrecp);
                            $lignecp->setLongeurArbrecp((int) $arraydata->longeur_arbrecp);
                            $lignecp->setDiametreArbrecp((int) $arraydata->diametre_arbrecp);
                            $lignecp->setVolumeArbrecp((float)$arraydata->volume_arbrecp);

                            $lignecp->setLongeuraBillecp((int) $arraydata->longeura_billecp);
                            $lignecp->setDiametreaBillecp((int) $arraydata->diametrea_billecp);
                            $lignecp->setVolumeaBillecp((float)$arraydata->volumea_billecp);

                            $lignecp->setLongeurbBillecp((int) $arraydata->longeurb_billecp);
                            $lignecp->setDiametrebBillecp((int) $arraydata->diametreb_billecp);
                            $lignecp->setVolumebBillecp((float)$arraydata->volumeb_billecp);

                            $lignecp->setLongeurcBillecp((int) $arraydata->longeurc_billecp);
                            $lignecp->setDiametrecBillecp((int) $arraydata->diametrec_billecp);
                            $lignecp->setVolumecBillecp((float)$arraydata->volumec_billecp);


                            $lignecp->setAUtlise(false);

                            if ($arraydata->longeurb_billecp){
                                $lignecp->setBUtilise(false);
                            } else {
                                $lignecp->setBUtilise(true);
                            }

                            if ($arraydata->longeurc_billecp){
                                $lignecp->setCUtilise(false);
                            } else {
                                $lignecp->setCUtilise(true);
                            }


                            /*Vérifie si le Fût est abandonné*/
                            if ($arraydata->abandon_fut == "true"){
                                $lignecp->setFutAbandon(true);
                            } else {
                                $lignecp->setFutAbandon(false);
                            }

                            /*Vérifie si la bille A est abandonnée*/
                            if ($arraydata->abandon_a == "true"){
                                $lignecp->setAAbandon(true);
                                $lignecp->setAUtlise(true);
                            } else {
                                $lignecp->setAAbandon(false);
                            }

                            /*Vérifie si la bille B est abandonnée*/
                            if ($arraydata->abandon_b == "true"){
                                $lignecp->setBAbandon(true);
                                $lignecp->setBUtilise(true);
                            } else {
                                $lignecp->setBAbandon(false);
                            }

                            /*Vérifie si la bille C est abandonnée*/
                            if ($arraydata->abandon_c == "true"){
                                $lignecp->setCAbandon(true);
                                $lignecp->setCUtilise(true);
                            } else {
                                $lignecp->setCAbandon(false);
                            }


                            $lignecp->setCreatedAt($date_jour);
                            $lignecp->setCreatedBy($user);
                            $lignecp->setCodePagecp($registry->getRepository(Pagecp::class)->find((int) $arraydata->code_pagecp));


                            //Vérifie si la bille provient de l'inventaire et l'ajoute
                            if ($arraydata->id_inv){
                                //recherche la ligne correspondant à l'arbre inventorié
                                $arbre_inv = $registry->getRepository(InventaireForestier::class)->find((int) $arraydata->id_inv);
                                //dd($arbre_inv);
                                if($arbre_inv){
                                    $lignecp->setCodeInv($arbre_inv);
                                }
                            }

                            $registry->getManager()->persist($lignecp);

                            // Mise à jour du numero CC de la foret
                            $registry->getRepository(Foret::class)->find($id_foret)->setDernierNumero($lignecp->getNumeroArbrecp());

                            $registry->getManager()->flush();

                            //Ajout de la donnée dans SNVLT
                            $this->snvlt1->AddLignePageDoccp($lignecp);

                            $response = array();
                            $response[] = array(
                                'code_cp'=>'LIGNE_CC_ADDED_SUCCESSFULLY',
                                'exo'=>$this->administrationService->getAnnee()->getAnnee()
                            );
                        } else {
                            $response[] = array(
                                'code_cp'=>'LIGNE_CC_NO_SPECIES_OR_ZONE',
                                'exo'=>$this->administrationService->getAnnee()->getAnnee()
                            );
                        }



                    return  new JsonResponse(json_encode($response));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

#[Route('/snvlt/doccp/op/pages_cp/data/add_lignes/abd/{data}/{id_foret}', name: 'adddata_cp_json_abandon')]
    public function add_lignes_cp_abandon(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        string $data,
        int $id_foret,
        NotificationRepository $notification,
        PagecpRepository $pages_cp,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_DPIF')  or $this->isGranted('ROLE_DPIF_SAISIE'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                //$page_cp = $pages_cp->find($id_page);
                if($data){
                    $lignecp = new Lignepagecp();


                    //Decoder le JSON BRH
                    $arraydata = json_decode($data);

                    // dd((int) $arraydata->numero_lignepagecp . " - " . $arraydata->lettre_lignepagecp);
                    $essence =  $registry->getRepository(Essence::class)->find((int)  $arraydata->nom_essencecp);
                    $zone = $registry->getRepository(ZoneHemispherique::class)->find((int) $arraydata->zh_arbrecp);
                    if($essence && $zone){

                        //dd($arraydata->numero_lignepagecp);
                        $date_jour = new \DateTime();
                        $lignecp->setNumeroArbrecp((int) $arraydata->numero_arbrecp);
                        $lignecp->setNomEssencecp($essence);
                        $lignecp->setZhArbrecp($zone);
                        $lignecp->setJourAbattage($arraydata->jour_abattage);
                        $lignecp->setXArbrecp((float) $arraydata->x_arbrecp);
                        $lignecp->setYArbrecp((float)$arraydata->y_arbrecp);
                        $lignecp->setLongeurArbrecp((int) $arraydata->longeur_arbrecp);
                        $lignecp->setDiametreArbrecp((int) $arraydata->diametre_arbrecp);
                        $lignecp->setVolumeArbrecp((float)$arraydata->volume_arbrecp);


                        $lignecp->setLongeuraBillecp(0);
                        $lignecp->setDiametreaBillecp(0);
                        $lignecp->setVolumeaBillecp(0);

                        $lignecp->setLongeurbBillecp(0);
                        $lignecp->setDiametrebBillecp(0);
                        $lignecp->setVolumebBillecp(0);

                        $lignecp->setLongeurcBillecp(0);
                        $lignecp->setDiametrecBillecp(0);
                        $lignecp->setVolumecBillecp(0);

                        $lignecp->setFutAbandon(true);


                        $lignecp->setCreatedAt($date_jour);
                        $lignecp->setCreatedBy($user);
                        $lignecp->setCodePagecp($registry->getRepository(Pagecp::class)->find((int) $arraydata->code_pagecp));


                        //Vérifie si la bille provient de l'inventaire et l'ajoute
                        if ($arraydata->id_inv){
                            //recherche la ligne correspondant à l'arbre inventorié
                            $arbre_inv = $registry->getRepository(InventaireForestier::class)->find((int) $arraydata->id_inv);
                            //dd($arbre_inv);
                            if($arbre_inv){
                                $lignecp->setCodeInv($arbre_inv);
                            }
                        }

                        $registry->getManager()->persist($lignecp);

                        // Mise à jour du numero CC de la foret
                        $registry->getRepository(Foret::class)->find($id_foret)->setDernierNumero($lignecp->getNumeroArbrecp());

                        $registry->getManager()->flush();

                        //Ajout de la donnée dans SNVLT
                        $this->snvlt1->AddLignePageDoccp($lignecp);

                        $response = array();
                        $response[] = array(
                            'code_cp'=>'LIGNE_CC_ADDED_SUCCESSFULLY',
                            'exo'=>$this->administrationService->getAnnee()->getAnnee()
                        );

                    }

                    return  new JsonResponse(json_encode($response));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    #[Route('/snvlt/doccp/op/pages_cp/data/edit_lignes/{data}/{id_ligne}', name: 'edit_ligne_pagecp')]
    public function edit_ligne_pagecp(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        string $data,
        int $id_ligne,
        NotificationRepository $notification,
        PagecpRepository $pages_cp,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_DPIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                //$page_cp = $pages_cp->find($id_page);
                if($data){
                    $lignecp = $registry->getRepository(Lignepagecp::class)->find($id_ligne);


                    //Decoder le JSON BRH
                    $arraydata = json_decode($data);


                    if($lignecp){

                        //dd($arraydata->numero_lignepagecp);
                        $date_jour = new \DateTime();
                        $essence = $registry->getRepository(Essence::class)->find((int)  $arraydata->nom_essencecp);
                        if ($essence){
                            $lignecp->setNomEssencecp($essence);
                        }

                        $zh = $registry->getRepository(ZoneHemispherique::class)->find((int)  $arraydata->zh_arbrecp);
                        if ($zh){
                            $lignecp->setZhArbrecp($zh);
                        }

                        //dd($registry->getRepository(Essence::class)->find((int)  $arraydata->nom_essencecp));
                        $lignecp->setJourAbattage($arraydata->jour_abattage);
                        $lignecp->setXArbrecp((float) $arraydata->x_arbrecp);
                        $lignecp->setYArbrecp((float)$arraydata->y_arbrecp);
                        $lignecp->setLongeurArbrecp((int) $arraydata->longeur_arbrecp);
                        $lignecp->setDiametreArbrecp((int) $arraydata->diametre_arbrecp);
                        $lignecp->setVolumeArbrecp((float)$arraydata->volume_arbrecp);

                        $lignecp->setLongeuraBillecp((int) $arraydata->longeura_billecp);
                        $lignecp->setDiametreaBillecp((int) $arraydata->diametrea_billecp);
                        $lignecp->setVolumeaBillecp((float)$arraydata->volumea_billecp);

                        $lignecp->setLongeurbBillecp((int) $arraydata->longeurb_billecp);
                        $lignecp->setDiametrebBillecp((int) $arraydata->diametreb_billecp);
                        $lignecp->setVolumebBillecp((float)$arraydata->volumeb_billecp);

                        $lignecp->setLongeurcBillecp((int) $arraydata->longeurc_billecp);
                        $lignecp->setDiametrecBillecp((int) $arraydata->diametrec_billecp);
                        $lignecp->setVolumecBillecp((float)$arraydata->volumec_billecp);


                        $lignecp->setCreatedAt($date_jour);
                        $lignecp->setCreatedBy($user);

                        $registry->getManager()->persist($lignecp);


                        $registry->getManager()->flush();

                        //Enregistrement dans SNVLT1
                        $this->snvlt1->EditLignePageDoccp($lignecp);

                        $response = array();
                        $response[] = array(
                            'code_cp'=>'LIGNE_CC_ADDED_SUCCESSFULLY',
                            'exo'=>$this->administrationService->getAnnee()->getAnnee()
                        );

                    } /*else {
                        $response[] = array(
                            'code_cp'=>'SAME_NUMBER',
                            'exo'=>$this->administrationService->getAnnee()->getAnnee()
                        );

                    }*/
                    return  new JsonResponse(json_encode($response));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    #[Route('/snvlt/attributions/stock', name: 'stock_attributions')]
    public function stock_attributions(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
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

                $liste_attributions = array();

                if($user->getCodeOperateur()->getId() == 1){
                    $mes_attribution = $registry->getRepository(Attribution::class)->findAll();
                    foreach($mes_attribution as $attribution){
                        $liste_attributions[] = array(
                            'id_attribution'=>$attribution->getCodeForet()->getId(),
                            'denomination'=>$attribution->getCodeForet()->getDenomination()
                        );
                    }

                } elseif ($user->getCodeexploitant()){
                $mes_attribution = $registry->getRepository(Attribution::class)->findBy(['code_exploitant'=>$user->getCodeexploitant()]);
                    foreach($mes_attribution as $attribution){
                        $liste_attributions[] = array(
                            'id_attribution'=>$attribution->getCodeForet()->getId(),
                            'denomination'=>$attribution->getCodeForet()->getDenomination()
                        );
                    }
                } elseif ($user->getCodeDr()){
                    $cantonnemants = $registry->getRepository(Cantonnement::class)->findBy(['code_dr'=>$user->getCodeDr()]);
                    foreach ($cantonnemants as $cantonnemant){
                        $forets = $registry->getRepository(Foret::class)->findBy(['code_cantonnement'=>$cantonnemant]);
                        foreach ($forets as $foret){
                            $mes_attribution = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$foret]);
                            foreach($mes_attribution as $attribution){
                                $liste_attributions[] = array(
                                    'id_attribution'=>$attribution->getCodeForet()->getId(),
                                    'denomination'=>$attribution->getCodeForet()->getDenomination()
                                );
                            }
                        }
                    }

                 } elseif ($user->getCodeDdef()){
                    $cantonnemants = $registry->getRepository(Cantonnement::class)->findBy(['code_ddef'=>$user->getCodeDdef()]);
                    foreach ($cantonnemants as $cantonnemant){
                        $forets = $registry->getRepository(Foret::class)->findBy(['code_cantonnement'=>$cantonnemant]);
                        foreach ($forets as $foret){
                            $mes_attribution = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$foret]);
                            foreach($mes_attribution as $attribution){
                                $liste_attributions[] = array(
                                    'id_attribution'=>$attribution->getCodeForet()->getId(),
                                    'denomination'=>$attribution->getCodeForet()->getDenomination()
                                );
                            }
                        }
                    }

                } elseif ($user->getCodeCantonnement()){

                        $forets = $registry->getRepository(Foret::class)->findBy(['code_cantonnement'=>$user->getCodeCantonnement()]);
                        foreach ($forets as $foret){
                            $mes_attribution = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$foret]);
                            foreach($mes_attribution as $attribution){
                                $liste_attributions[] = array(
                                    'id_attribution'=>$attribution->getCodeForet()->getId(),
                                    'denomination'=>$attribution->getCodeForet()->getDenomination()
                                );
                            }
                        }


                }

                return new JsonResponse(json_encode($liste_attributions));


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/liste_forets/lst/', name: 'app_liste_forets')]
    public function app_liste_forets(
        Request $request,
        UserRepository $userRepository,
        ManagerRegistry $registry
    ): Response
    {

        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $liste_forets = array();
                //------------------------- Filtre les CC par type Opérateur ------------------------------------- //
                if ($user->getCodeOperateur()->getId() == 1){
                    $forets = $registry->getRepository(Foret::class)->findBy(['reprise'=>true]);
                    //dd($forets);
                    foreach ($forets as $foret){

                        $liste_forets[] = array(
                            'numero_foret'=>$foret->getDenomination(),
                            'id_foret'=>$foret->getId()

                        );

                    }
                } elseif ($user->getCodeOperateur()->getId() == 2){
                    $attributions = $registry->getRepository(Attribution::class)->findBy(['code_exploitant'=>$user->getCodeexploitant(), 'statut'=>true]);

                    foreach ($attributions as $attribution){

                        $liste_forets[] = array(
                            'numero_foret'=>$attribution->getCodeForet()->getDenomination(),
                            'id_foret'=>$attribution->getCodeForet()->getId()

                        );

                    }
                } elseif ($user->getCodeOperateur()->getId() == 5){

                    $cantonnements = $registry->getRepository(Cantonnement::class)->findBy(['code_dr'=>$user->getCodeDr()]);
                    foreach($cantonnements as $cantonnement){
                        $forets = $registry->getRepository(Foret::class)->findBy(['code_cantonnement'=>$cantonnement]);
                        foreach ($forets as $foret){

                            $liste_forets[] = array(
                                'numero_foret'=>$foret->getDenomination(),
                                'id_foret'=>$foret->getId()

                            );

                        }
                    }


                }  elseif ($user->getCodeOperateur()->getId() == 6){

                    $cantonnements = $registry->getRepository(Cantonnement::class)->findBy(['code_ddef'=>$user->getCodeDdef()]);
                    foreach($cantonnements as $cantonnement){
                        $forets = $registry->getRepository(Foret::class)->findBy(['code_cantonnement'=>$cantonnement]);
                        foreach ($forets as $foret){

                            $liste_forets[] = array(
                                'numero_foret'=>$foret->getDenomination(),
                                'id_foret'=>$foret->getId()

                            );

                        }
                    }
                } elseif ($user->getCodeOperateur()->getId() == 7){
                    $forets = $registry->getRepository(Foret::class)->findBy(['code_cantonnement'=>$user->getCodeCantonnement()]);
                        foreach ($forets as $foret){
                            $liste_forets[] = array(
                                'numero_foret'=>$foret->getDenomination(),
                                'id_foret'=>$foret->getId()

                            );

                        }

                }


                return  new JsonResponse(json_encode($liste_forets));
                    } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }



        }
    }
    #[Route('/snvlt/liste_forets/lst/{id_exploitant}', name: 'liste_forets_exp')]
    public function liste_forets_exp(
        Request $request,
        int $id_exploitant,
        UserRepository $userRepository,
        ManagerRegistry $registry
    ): Response
    {

        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if (
                $this->isGranted('ROLE_DD') or
                $this->isGranted('ROLE_CEF') or
                $this->isGranted('ROLE_DR') or
                $this->isGranted('ROLE_MINEF') or
                $this->isGranted('ROLE_EXPLOITANT')or
                $this->isGranted('ROLE_INDUSTRIEL')or
                $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $liste_forets = array();

                $exp = $registry->getRepository(Exploitant::class)->find($id_exploitant);
                if ($exp){
                    $attributions = $registry->getRepository(Attribution::class)->findBy(['code_exploitant'=>$exp, 'exercice'=>$this->administrationService->getAnnee(), 'statut'=>true]);
                    foreach($attributions as $attribution){
                        $liste_forets[] = array(
                            'id_foret'=>$attribution->getCodeForet()->getId(),
                            'numero_foret'=>$attribution->getCodeForet()->getDenomination()
                        );
                    }
                }


                return  new JsonResponse(json_encode($liste_forets));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }



        }
    }
    #[Route('/snvlt/affiche_ligne/cp/{id_ligne}', name: 'app_search_ligne_cp')]
    public function app_search_ligne_cp(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_ligne,
        NotificationRepository $notification,
        DocumentcpRepository $docs_cp,
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

                $liste_arbres = array();
                //------------------------- Filtre les CC par type Opérateur ------------------------------------- //

                $lignecp = $registry->getRepository(Lignepagecp::class)->find($id_ligne);

                if ($lignecp){
                    $liste_arbres[] = array(
                        'id_ligne'=>$lignecp->getId(),
                        'numero_ligne'=>$lignecp->getNumeroArbrecp(),
                        'essence'=>$lignecp->getNomEssencecp()->getId(),
                        'x_arbre'=>$lignecp->getXArbrecp(),
                        'y_arbre'=>$lignecp->getYArbrecp(),
                        'zh_arbre'=>$lignecp->getZhArbrecp()->getId(),
                        'jour'=>$lignecp->getJourAbattage(),
                        'lng_arbre'=>$lignecp->getLongeurArbrecp(),
                        'dm_arbre'=>$lignecp->getDiametreArbrecp(),
                        'cubage_arbre'=>$lignecp->getVolumeArbrecp(),
                        'lng_billea'=>$lignecp->getLongeuraBillecp(),
                        'dm_billea'=>$lignecp->getDiametreaBillecp(),
                        'cubage_billea'=>$lignecp->getVolumeaBillecp(),
                        'lng_billeb'=>$lignecp->getLongeurbBillecp(),
                        'dm_billeb'=>$lignecp->getDiametrebBillecp(),
                        'cubage_billeb'=>$lignecp->getVolumebBillecp(),
                        'lng_billec'=>$lignecp->getLongeurcBillecp(),
                        'dm_billec'=>$lignecp->getDiametrecBillecp(),
                        'cubage_billec'=>$lignecp->getVolumecBillecp(),
                        'a_utilise'=>$lignecp->isAUtlise(),
                        'b_utilise'=>$lignecp->isBUtilise(),
                        'c_utilise'=>$lignecp->isCUtilise(),
                        'a_abandon'=>$lignecp->isAAbandon(),
                        'b_abandon'=>$lignecp->isBAbandon(),
                        'c_abandon'=>$lignecp->isCAbandon(),
                        'fut_abandon'=>$lignecp->isFutAbandon()
                    );
                }
                return  new JsonResponse(json_encode($liste_arbres));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/maj/numero_arbre/{id_arbre}/{numero}', name: 'edit_numero_arbre')]
    public function edit_numero_arbre(
        Request $request,
        UserRepository $userRepository,
        int $id_arbre,
        int $numero,
        ManagerRegistry $registry
    ): Response
    {

        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')  )
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $resultat = array();
                //------------------------- Filtre les CC par type Opérateur ------------------------------------- //

                $foret= $registry->getRepository(Foret::class)->find($id_arbre);

                if ($foret){
                    $foret->setDernierNumero((int) $numero);
                    $registry->getManager()->persist($foret);
                    $registry->getManager()->flush();
                    $resultat[] = array(
                        'code'=>'SUCCESS'
                    );
                } else {
                    $resultat[] = array(
                        'code'=>'ERROR'
                    );
                }
                return  new JsonResponse(json_encode($resultat));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
    #[Route('snvlt/doccp/op/pages_cp/data/verif/{numero_arbre}/{id_foret}', name: 'search_ligne_cp')]
    public function search_ligne_cp(
        Request $request,
        UserRepository $userRepository,
        string $numero_arbre,
        int $id_foret,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT')  or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE'))
            {
                if($numero_arbre and $id_foret){

                    $isSameValue = false;
                    $response = array();
                    //Recherche la foret

                    $mes_attributions = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$registry->getRepository(Foret::class)->find($id_foret)]);
                    foreach($mes_attributions as $attribution){
                        $mes_reprises =$registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution]);
                        foreach($mes_reprises as $reprise){
                            $mes_cp = $registry->getRepository(Documentcp::class)->findBy(['code_reprise'=>$reprise]);
                            foreach($mes_cp as $cp){
                                $mes_pages = $registry->getRepository(Pagecp::class)->findBy(['code_doccp'=>$cp]);
                                foreach($mes_pages as $pagecp){
                                    $mes_lignes = $registry->getRepository(Lignepagecp::class)->findBy(['code_pagecp'=>$pagecp]);
                                    foreach($mes_lignes as $ligne){
                                        $numero = $ligne->getNumeroArbrecp();
                                        //dd($numero);
                                        if ($numero == $numero_arbre){
                                            $isSameValue = true;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    // dd((int) $arraydata->numero_lignepagecp . " - " . $arraydata->lettre_lignepagecp);
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
}