<?php

namespace App\Controller;

use App\Controller\Services\AdministrationService;
use App\Controller\Services\synchro\UserSnvlt1;
use App\Entity\Admin\Exercice;
use App\Entity\Admin\LogSnvlt;
use App\Entity\Autorisation\AgreementExportateur;
use App\Entity\Autorisation\AgreementPs;
use App\Entity\Autorisation\Attribution;
use App\Entity\Autorisation\AttributionPv;
use App\Entity\Autorisation\AutorisationExportateur;
use App\Entity\Autorisation\AutorisationPs;
use App\Entity\Autorisation\AutorisationPv;
use App\Entity\Autorisation\Reprise;
use App\Entity\DisponibiliteParcBilles;
use App\Entity\DisponibiliteParcBillons;
use App\Entity\DocStats\Entetes\Documentbcbgfh;
use App\Entity\DocStats\Entetes\Documentbcbp;
use App\Entity\DocStats\Entetes\Documentbcburb;
use App\Entity\DocStats\Entetes\Documentbrepf;
use App\Entity\DocStats\Entetes\Documentbrh;
use App\Entity\DocStats\Entetes\Documentbtgu;
use App\Entity\DocStats\Entetes\Documentcp;
use App\Entity\DocStats\Entetes\Documentdmp;
use App\Entity\DocStats\Entetes\Documentdmv;
use App\Entity\DocStats\Entetes\Documentetatb;
use App\Entity\DocStats\Entetes\Documentetate;
use App\Entity\DocStats\Entetes\Documentetate2;
use App\Entity\DocStats\Entetes\Documentetatg;
use App\Entity\DocStats\Entetes\Documentetath;
use App\Entity\DocStats\Entetes\Documentfp;
use App\Entity\DocStats\Entetes\Documentlje;
use App\Entity\DocStats\Entetes\Documentpdtdrv;
use App\Entity\DocStats\Entetes\Documentrsdpf;
use App\Entity\DocStats\Pages\Pagebcbp;
use App\Entity\DocStats\Pages\Pagebrh;
use App\Entity\DocStats\Pages\Pagecp;
use App\Entity\DocStats\Pages\Pagelje;
use App\Entity\DocStats\Saisie\Lignepagebcbgfh;
use App\Entity\DocStats\Saisie\Lignepagebcbp;
use App\Entity\DocStats\Saisie\Lignepagebrh;
use App\Entity\DocStats\Saisie\Lignepagecp;
use App\Entity\DocStats\Saisie\Lignepagelje;
use App\Entity\Observateur\PublicationRapport;
use App\Entity\Observateur\Ticket;
use App\Entity\References\Cantonnement;
use App\Entity\References\Dr;
use App\Entity\References\Essence;
use App\Entity\References\Exploitant;
use App\Entity\References\Foret;
use App\Entity\References\TypeDocumentStatistique;
use App\Entity\References\TypeOperateur;
use App\Entity\References\Usine;
use App\Entity\Requetes\PerformanceBrhJour;
use App\Entity\Transformation\Billon;
use App\Entity\User;
use App\Entity\Vues\ChargementCours;
use App\Repository\Administration\NotificationRepository;
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
use Symfony\Contracts\Translation\TranslatorInterface;

class TdbAdminController extends AbstractController
{
    private $translator;
    public function __construct(TranslatorInterface $translator, private AdministrationService $administrationService, private UserSnvlt1 $snvlt1)
    {
        $this->translator = $translator;
    }

    #[Route('/snvlt/admin', name: 'app_tdb_admin')]
    public function index(
        Request $request,
        MenuRepository $menus,
        ManagerRegistry $registry,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
    ): Response
    {
        //dd($this->getUser());


        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            $user = $userRepository->find($this->getUser());
            $request->getSession()->set('id_session', $user->getId());
        }
        if (!$user->isVerified()){
            return $this->render('exceptions/non_verifie.html.twig');
        } elseif (!$user->getActif()){
            return $this->redirectToRoute('user_not_active');
        }
        $exo = $request->getSession()->get("exercice");

        $exercice = $registry->getRepository(Exercice::class)->find($exo);

        //dd($exo);

        $code_groupe = $user->getCodeGroupe()->getId();
        //$logsnvlt = $registry->getRepository(LogSnvlt::class)->findBy([], ['created_at'=>'DESC'], 20, 0);

        $page_brh = $registry->getRepository(Pagebrh::class)->findBy(['fini' => true]);

        //Déclarations
        $liste_essences_exploitees = array();
        $chargements_cef = array();
        $liste_essences_vol = array();
        $liste_doc_brh= array();
        $liste_quotas= array();

        $point_ddef = array();
        $quotas_dr = array();
        $volume_foret = array();
        $essence_disponibles = array();
        $billons_disponibles = array();
        $liste_billes_industriel = array();
        $reprises_count = 0;
        $nb_brh_op = 0;
        $nb_cp_op = 0;
        $nb_bcbp_op = 0;
        $nb_etatb_op = 0;
        //$nb_users = 0;
        //$nb_autorisations = 0;
        $total_exploitation = 0;
        $total_transformation= 0;
        $total_agreements = 0;
        $total_autorisations = 0;
        $liste_essences_admin = array();
        $arbres_abattus = 0;
        $volume_brh = 0;
        $nb_utilisateurs = 0;

// Interface DR
        if ($user->getCodeDr()){

            //Chargements DR
            $codedr = $user->getCodeDr();
            $cantonnements = $registry->getRepository(Cantonnement::class)->findBy(['code_dr' => $user->getCodeDr()]);
            foreach ($cantonnements as $cantonnement) {
                $exploitants = $registry->getRepository(Exploitant::class)->findBy(['code_cantonnement' => $cantonnement]);
                foreach ($exploitants as $exploitant) {
                    $attributions = $registry->getRepository(Attribution::class)->findBy(['code_exploitant' => $exploitant]);
                    foreach ($attributions as $attribution) {
                        $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution' => $attribution, 'exercice'=>$exercice]);
                        foreach ($reprises as $reprise) {
                            $docbrh = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise' => $reprise]);
                            foreach ($docbrh as $doc) {
                                $page_brh = $registry->getRepository(Pagebrh::class)->findBy(['code_docbrh' => $doc, 'fini' => true]);
                            }
                        }
                    }
                }
            }


            // Quotas DR

            //$cantonnements = $registry->getRepository(Cantonnement::class)->findBy(['code_dr' => $user->getCodeDr()]);
            foreach ($cantonnements as $cantonnement) {
                $forets = $registry->getRepository(Foret::class)->findBy(['code_cantonnement' => $cantonnement]);
                foreach ($forets as $foret) {
                    $attributions = $registry->getRepository(Attribution::class)->findBy(['code_foret' => $foret]);
                    foreach($attributions as $attrib){
                        $reprises  = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attrib, 'exercice'=>$exercice]);
                        foreach($reprises as $reprise){
                            $vol_quota = 0;
                            //$reprises_count = $reprises_count +1;
                            $doccps  = $registry->getRepository(Documentcp::class)->findBy(['code_reprise'=>$reprise]);
                            foreach($doccps as $doccp){
                                //$vol = 0;

                                $pagecps  = $registry->getRepository(Pagecp::class)->findBy(['code_doccp'=>$doccp]);
                                foreach($pagecps as $pagecp){
                                    $lignepagecps  =$registry->getRepository(Lignepagecp::class)->findBy(['code_pagecp'=>$pagecp]);
                                    foreach($lignepagecps as $lignecp){
                                        $vol_quota  = $vol_quota  + $lignecp->getVolumeArbrecp();
                                        // $vol = $vol + $lignecp->getCubageLignepagebrh();
                                    }
                                }

                            }
                            $liste_quotas[] = array(
                                'foret'=>$attrib->getCodeForet()->getDenomination(),
                                'quota_val'=>round(($attrib->getCodeForet()->getSuperficie() / 4),3),
                                'cubage'=>round($vol_quota, 3)
                            );
                        }
                    }
                }
            }

            // Cubage BRH Total
            foreach ($cantonnements as $cantonnement) {
                $forets = $registry->getRepository(Foret::class)->findBy(['code_cantonnement' => $cantonnement]);
                foreach ($forets as $foret) {
                    $attributions = $registry->getRepository(Attribution::class)->findBy(['code_foret' => $foret]);
                    foreach($attributions as $attrib){
                        $reprises  = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attrib, 'exercice'=>$exercice]);
                        foreach($reprises as $reprise){
                            $vol_quota = 0;
                            //$reprises_count = $reprises_count +1;
                            $docbrhs  = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$reprise]);
                            foreach($docbrhs as $docbrh){
                                //$vol = 0;

                                $pagebrhs  = $registry->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$docbrh]);
                                foreach($pagebrhs as $pagebrh){
                                    $lignepagebrhs  =$registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$pagebrh]);
                                    foreach($lignepagebrhs as $lignebrh){
                                        $volume_brh = $volume_brh + $lignebrh->getCubageLignepagebrh();
                                        // $vol = $vol + $lignecp->getCubageLignepagebrh();
                                    }
                                }

                            }
                            $liste_quotas[] = array(
                                'foret'=>$attrib->getCodeForet()->getDenomination(),
                                'quota_val'=>round(($attrib->getCodeForet()->getSuperficie() / 4),3),
                                'cubage'=>round($vol_quota, 3)
                            );
                        }
                    }
                }
            }

            // Nb Agents
            foreach($user->getCodeDr()->getUsers() as $nb_dr){
                $nb_utilisateurs = $nb_utilisateurs + 1;
            }

            foreach($user->getCodeDr()->getDdefs() as $nb_ddef){
                $nb_utilisateurs = $nb_utilisateurs + $nb_ddef->getUsers()->count();
            }
            foreach($user->getCodeDr()->getCantonnements() as $nb_cef){
                $nb_utilisateurs = $nb_utilisateurs + $nb_cef->getUsers()->count();
                foreach($nb_cef->getPosteForestiers() as $nb_pf){
                    $nb_utilisateurs = $nb_utilisateurs + $nb_pf->getUsers()->count();
                }
            }
        }

        // Interface DDEF
        if ($user->getCodeDdef()){
            $codeddef = $user->getCodeDdef();
            $cantonnements = $registry->getRepository(Cantonnement::class)->findBy(['code_ddef' => $user->getCodeDdef()]);
            foreach ($cantonnements as $cantonnement) {
                $exploitants = $registry->getRepository(Exploitant::class)->findBy(['code_cantonnement' => $cantonnement]);
                foreach ($exploitants as $exploitant) {
                    $attributions = $registry->getRepository(Attribution::class)->findBy(['code_exploitant' => $exploitant]);
                    foreach ($attributions as $attribution) {
                        $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution' => $attribution, 'exercice'=>$exercice]);
                        foreach ($reprises as $reprise) {
                            $docbrh = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise' => $reprise]);
                            foreach ($docbrh as $doc) {
                                $page_brh = $registry->getRepository(Pagebrh::class)->findBy(['code_docbrh' => $doc, 'fini' => true]);
                            }
                        }
                    }
                }
            }


            //Point Opérateurs
            $cantons = $registry->getRepository(Cantonnement::class)->findBy(['code_ddef'=>$codeddef]);
            foreach ($cantons as $canton ){
                $forets = $canton->getForets();
                foreach ($forets as $foret){
                    $exploitant = "-";
                    $nb_cp = 0;
                    $nb_brh = 0;
                    $arbre_abattus = 0;
                    $volume_abattage = 0;
                    $volume_brh = 0;
                    $decision_reprise = "-";
                    $decision_attribution = "-";

                    $attributions = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$foret, 'statut'=>true]);
                    foreach($attributions as $attribution){

                        if ($attribution->getCodeExploitant()->getSigle()){
                            $exploitant = $attribution->getCodeExploitant()->getSigle();
                        } else {
                            $exploitant = $attribution->getCodeExploitant()->getRaisonSocialeExploitant();
                        }

                        $decision_attribution = $attribution->getNumeroDecision(). " du ". $attribution->getDateDecision()->format('d/m/Y');
                        $rep = $registry->getRepository(Reprise::class)->findOneBy(['code_attribution'=>$attribution, 'exercice'=>$exercice]);

                        if ($rep){
                            $decision_reprise = $rep->getNumeroAutorisation(). " du ". $rep->getDateAutorisation()->format('d/m/Y');
                        }

                        $doc_cps = $registry->getRepository(Documentcp::class)->findBy(['code_reprise'=>$rep]);
                        $doc_brhs = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$rep]);
                        foreach ($doc_cps as $doc_cp){
                            $nb_cp = $nb_cp + 1;
                            $arbres_abattus = $nb_cp;
                            $pagescp = $registry->getRepository(Pagecp::class)->findBy(['code_doccp'=>$doc_cp]);
                            foreach ($pagescp as $page){
                                $lignecps = $registry->getRepository(Lignepagecp::class)->findBy(['code_pagecp'=>$page]);
                                foreach($lignecps as $lignecp){
                                    $volume_abattage = $volume_abattage + $lignecp->getVolumeArbrecp();
                                    $arbre_abattus = $arbre_abattus + 1;
                                }
                            }
                        }
                        foreach ($doc_brhs as $doc_brh){
                            $nb_brh = $nb_brh + 1;
                            $pagesbrh = $registry->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$doc_brh]);
                            foreach ($pagesbrh as $page){
                                $lignebrhs = $registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$page]);
                                foreach($lignebrhs as $lignebrh){
                                    $volume_brh = $volume_brh + $lignebrh->getCubageLignepagebrh();
                                }
                            }
                        }
                        $point_ddef[] = array(
                            'foret'=>$foret->getDenomination(),
                            'cantonnement'=>$canton,
                            'exploitant'=>$exploitant,
                            'decision_attribution'=>$decision_attribution,
                            'decision_reprise'=>$decision_reprise,
                            'nb_cp'=>$nb_cp,
                            'nb_brh'=>$nb_brh,
                            'nb_arbres_abattus'=>$arbre_abattus,
                            'volume_abattage'=>round($volume_abattage,3),
                            'volume_brh'=>round($volume_brh,3)
                        );
                    }
                }
            }


            // Quotas DDEF
            foreach ($cantonnements as $cantonnement) {
                $forets = $registry->getRepository(Foret::class)->findBy(['code_cantonnement' => $cantonnement]);
                foreach ($forets as $foret) {
                    $attributions = $registry->getRepository(Attribution::class)->findBy(['code_foret' => $foret]);
                    foreach($attributions as $attrib){
                        $reprises  = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attrib, 'exercice'=>$exercice]);
                        foreach($reprises as $reprise){
                            $vol_quota = 0;
                            //$reprises_count = $reprises_count +1;
                            $docbrhs  = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$reprise]);
                            foreach($docbrhs as $docbrh){
                                //$vol = 0;

                                $pagebrhs  = $registry->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$docbrh]);
                                foreach($pagebrhs as $pagebrh){
                                    $lignepagebrhs  =$registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$pagebrh]);
                                    foreach($lignepagebrhs as $lignebrh){
                                        $vol_quota  = $vol_quota  + $lignebrh->getCubageLignepagebrh();
                                        // $vol = $vol + $lignebrh->getCubageLignepagebrh();
                                    }
                                }

                            }
                            $liste_quotas[] = array(
                                'foret'=>$attrib->getCodeForet()->getDenomination(),
                                'quota_val'=>round(($attrib->getCodeForet()->getSuperficie() / 4),3),
                                'cubage'=>round($vol_quota, 3)
                            );
                        }
                    }
                }
            }

            // Cubage BRH Total
            foreach ($cantonnements as $cantonnement) {
                $forets = $registry->getRepository(Foret::class)->findBy(['code_cantonnement' => $cantonnement]);
                foreach ($forets as $foret) {
                    $attributions = $registry->getRepository(Attribution::class)->findBy(['code_foret' => $foret]);
                    foreach($attributions as $attrib){
                        $reprises  = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attrib, 'exercice'=>$exercice]);
                        foreach($reprises as $reprise){
                            $vol_quota = 0;
                            //$reprises_count = $reprises_count +1;
                            $docbrhs  = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$reprise]);
                            foreach($docbrhs as $docbrh){
                                //$vol = 0;

                                $pagebrhs  = $registry->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$docbrh]);
                                foreach($pagebrhs as $pagebrh){
                                    $lignepagebrhs  =$registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$pagebrh]);
                                    foreach($lignepagebrhs as $lignebrh){
                                        $volume_brh = $volume_brh + $lignebrh->getCubageLignepagebrh();
                                        // $vol = $vol + $lignecp->getCubageLignepagebrh();
                                    }
                                }

                            }
                            $liste_quotas[] = array(
                                'foret'=>$attrib->getCodeForet()->getDenomination(),
                                'quota_val'=>round(($attrib->getCodeForet()->getSuperficie() / 4),3),
                                'cubage'=>round($vol_quota, 3)
                            );
                        }
                    }
                }
            }

            // Nb Agents

            foreach($user->getCodeDdef()->getCantonnements() as $nb_cefs){
                $nb_utilisateurs = $nb_utilisateurs + $nb_cefs->getUsers()->count();
            }

            foreach($user->getCodeDdef()->getCantonnements() as $nb_cef){
                $nb_utilisateurs = $nb_utilisateurs + $nb_cef->getUsers()->count();
                foreach($nb_cef->getPosteForestiers() as $nb_pf){
                    $nb_utilisateurs = $nb_utilisateurs + $nb_pf->getUsers()->count();
                }
            }
        }

// Interfaces CEF
        if ($user->getCodeCantonnement()){
            // Point Opérateurs

            $forets = $user->getCodeCantonnement()->getForets();
            foreach ($forets as $foret){
                $exploitant = "-";
                $nb_cp = 0;
                $nb_brh = 0;
                $arbre_abattus = 0;
                $volume_abattage = 0;
                $volume_brh = 0;
                $decision_reprise = "-";
                $decision_attribution = "-";

                $attributions = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$foret, 'statut'=>true]);
                foreach($attributions as $attribution){

                    if ($attribution->getCodeExploitant()->getSigle()){
                        $exploitant = $attribution->getCodeExploitant()->getSigle();
                    } else {
                        $exploitant = $attribution->getCodeExploitant()->getRaisonSocialeExploitant();
                    }

                    $decision_attribution = $attribution->getNumeroDecision(). " du ". $attribution->getDateDecision()->format('d/m/Y');
                    $rep = $registry->getRepository(Reprise::class)->findOneBy(['code_attribution'=>$attribution, 'exercice'=>$exercice]);

                    if ($rep){
                        $decision_reprise = $rep->getNumeroAutorisation(). " du ". $rep->getDateAutorisation()->format('d/m/Y');
                    }

                    $doc_cps = $registry->getRepository(Documentcp::class)->findBy(['code_reprise'=>$rep]);
                    $doc_brhs = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$rep]);
                    foreach ($doc_cps as $doc_cp){
                        $nb_cp = $nb_cp + 1;
                        $arbres_abattus = $nb_cp;
                        $pagescp = $registry->getRepository(Pagecp::class)->findBy(['code_doccp'=>$doc_cp]);
                        foreach ($pagescp as $page){
                            $lignecps = $registry->getRepository(Lignepagecp::class)->findBy(['code_pagecp'=>$page]);
                            foreach($lignecps as $lignecp){
                                $volume_abattage = $volume_abattage + $lignecp->getVolumeArbrecp();
                                $arbre_abattus = $arbre_abattus + 1;
                            }
                        }
                    }
                    foreach ($doc_brhs as $doc_brh){
                        $nb_brh = $nb_brh + 1;
                        $pagesbrh = $registry->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$doc_brh]);
                        foreach ($pagesbrh as $page){
                            $lignebrhs = $registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$page]);
                            foreach($lignebrhs as $lignebrh){
                                $volume_brh = $volume_brh + $lignebrh->getCubageLignepagebrh();
                            }
                        }
                    }

                }
                $volume_foret[] = array(
                    'foret_cef'=>$foret->getDenomination(),
                    'volume_brh'=>round($volume_brh,3)
                );
            }



            // Chargements en cours dans le CEF
            // chargement depuis mon cantonnement
            foreach ($forets as $foret){
                $attributions_cef = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$foret, 'statut'=>true]);
                foreach($attributions as $attribution){
                    $rep_cef = $registry->getRepository(Reprise::class)->findOneBy(['code_attribution'=>$attribution, 'exercice'=>$exercice]);
                    if ($rep_cef){
                        foreach ($rep_cef as $rep){

                            $docbrh_cefs = $registry->getRepository(Documentbrh::class)->findBy(['$rep_cef'=>$rep]);
                            foreach ($docbrh_cefs as $doc){

                                $page_brh_cefs = $registry->getRepository(Pagebrh::class)->findBy(
                                    [
                                        'code_docbrh'=>$doc,
//                                    'fini'=>true,
//                                    'confirmation_usine'=>false
                                    ]
                                );
                                //dd($page_brh_cefs);
                                foreach ($page_brh_cefs as $page){
                                    $ligne_cef = $registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$page]);
                                    $vol_brh_cef = 0;
                                    $nb_billes_cef = 0;
                                    foreach ($ligne_cef as $ligne){
                                        $vol_brh_cef = $vol_brh_cef + $ligne->getCubageLignepagebrh();
                                        $nb_billes_cef = $nb_billes_cef + 1;
                                    }
                                    $destinataire = '-';
                                    $date_chr = '-';
                                    if ($page->getDateChargementbrh()){
                                        $date_chr = $page->getDateChargementbrh()->format('d/m/Y');
                                    }
                                    if ($page->getParcUsineBrh()){
                                        if ($page->getParcUsineBrh()->getSigle()){
                                            $destinataire = $page->getParcUsineBrh()->getSigle();
                                        } else {
                                            $destinataire = $page->getParcUsineBrh()->getRaisonSocialeUsine();
                                        }
                                    }

                                    $chargements_cef[] = array(
                                        'date_chargement'=>$date_chr,
                                        'numero_page'=>$page->getNumeroPagebrh(),
                                        'volume'=>round($vol_brh_cef, 3),
                                        'foret'=>$page->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination(),
                                        'nb_billes'=>$nb_billes_cef,
                                        'expediteur'=>$page->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getSigle(),
                                        'immatriculation'=>$page->getImmatcamion(),
                                        'destinataire'=>$destinataire,
                                        'mon_cef'=>1
                                    );

                                }
                            }
                        }
                    }
                }
            }

            // Chargements en partance pour mes CEF
            $usines_cef = $registry->getRepository(Usine::class)->findBy(['code_cantonnement'=>$user->getCodeCantonnement()]);
            foreach ($usines_cef as $usine){
                $toutes_les_pages = $registry->getRepository(Pagebrh::class)->findBy(
                    [
                        'parc_usine_brh'=>$usine,
                        'exercice'=>$exercice->getId()
//                        'fini'=>true,
//                        'confirmation_usine'=>false
                    ]
                );

                foreach ($toutes_les_pages as $page){
                    $ligne_cef = $registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$page]);
                    $vol_brh_cef = 0;
                    $nb_billes_cef = 0;
                    foreach ($ligne_cef as $ligne){
                        $vol_brh_cef = $vol_brh_cef + $ligne->getCubageLignepagebrh();
                        $nb_billes_cef = $nb_billes_cef + 1;
                    }
                    $destinataire = '-';
                    $date_chr = '-';
                    if ($page->getDateChargementbrh()){
                        $date_chr = $page->getDateChargementbrh()->format('d/m/Y');
                    }
                    if ($page->getParcUsineBrh()){
                        if ($page->getParcUsineBrh()->getSigle()){
                            $destinataire = $page->getParcUsineBrh()->getSigle();
                        } else {
                            $destinataire = $page->getParcUsineBrh()->getRaisonSocialeUsine();
                        }
                    }

                    $chargements_cef[] = array(
                        'date_chargement'=>$date_chr,
                        'numero_page'=>$page->getNumeroPagebrh(),
                        'volume'=>round($vol_brh_cef, 3),
                        'nb_billes'=>$nb_billes_cef,
                        'foret'=>$page->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination(),
                        'expediteur'=>$page->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getSigle(),
                        'immatriculation'=>$page->getImmatcamion(),
                        'destinataire'=>$destinataire,
                        'mon_cef'=>0
                    );

                }
            }

        }


        // Interface Exploitant
        if ($this->isGranted('ROLE_EXPLOITANT')){



            //$exercice = $this->administrationService->getAnnee();
            // essences et cubages

            $liste_essences = $registry->getRepository(Essence::class)->findAll();

            foreach($liste_essences as $ess){
                $linge_brh_essences_admin = $registry->getRepository(Lignepagebrh::class)->findBy(['nom_essencebrh'=>$ess, 'exercice'=>$exercice]);
                $vol_ess_brh = 0;
                foreach($linge_brh_essences_admin as $essence_brh){
                    $vol_ess_brh = $vol_ess_brh + $essence_brh->getCubageLignepagebrh();
                }
                $liste_essences_admin[] = array(
                    'volume'=>round($vol_ess_brh,0),
                    'essence'=>$ess->getNomVernaculaire()
                );
            }
            arsort($liste_essences_admin);

            //Quotas d'exploitation

            $attributions = $registry->getRepository(Attribution::class)->findBy(['code_exploitant'=>$user->getCodeexploitant(), 'exercice'=>$this->administrationService->getAnnee(), 'statut'=>true]);
            foreach($attributions as $attrib){
                $repris = $attrib->getReprises()->count(['exercice'=>$exercice, 'statut'=>true]);
                $reprises_count = $reprises_count + $repris;
            }
            //dd($reprises_count);
            //dd();
            foreach($attributions as $attrib){
                $reprises  = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attrib, 'statut'=>true]);
                foreach($reprises as $reprise){
                    $vol_quota = 0;
                    //$reprises_count = $reprises_count +1;
                    $docbrhs  = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$reprise]);
                    foreach($docbrhs as $docbrh){
                        //$vol = 0;

                        $pagebrhs  = $registry->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$docbrh]);
                        foreach($pagebrhs as $pagebrh){
                            $lignepagebrhs  =$registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$pagebrh]);

                            foreach($lignepagebrhs as $lignepagebrh){
                                $vol_quota  = $vol_quota  + $lignepagebrh->getCubageLignepagebrh();

                                // $vol = $vol + $lignecp->getCubageLignepagebrh();
                            }
                        }

                    }
                    $liste_quotas[] = array(
                        'foret'=>$attrib->getCodeForet()->getDenomination(),
                        'quota_val'=>round($attrib->getCodeForet()->getSuperficie() / 4),
                        'cubage'=>$vol_quota
                    );
                }
            }

            //Disponibilité Parc Chantier
            $attributions = $registry->getRepository(Attribution::class)->findBy(['code_exploitant'=>$user->getCodeexploitant()]);

            foreach ($attributions as $attribution){
                $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution, 'exercice'=>$exercice]);
                foreach ($reprises as $reprise){
                    $document_cp = $registry->getRepository(Documentcp::class)->findBy(['code_reprise'=>$reprise]);
                    foreach ($document_cp as $doc){
                        $pagecps = $registry->getRepository(Pagecp::class)->findBy(['code_doccp'=>$doc]);
                        foreach ($pagecps as $page){
                            $lignecps = $registry->getRepository(Lignepagecp::class)->findBy(['code_pagecp'=>$page]);
                            foreach ($lignecps as $lignecp){


                                if (!$lignecp->isAUtlise() && $lignecp->getLongeuraBillecp()){}
                                $zh = "-";
                                $essence = "-";
                                if ($lignecp->getZhArbrecp()) {
                                    $zh = $lignecp->getZhArbrecp()->getZone();
                                }
                                if ($lignecp->getNomEssencecp()) {
                                    $essence = $lignecp->getNomEssencecp()->getNomVernaculaire();
                                }
                                $liste_arbres[] = array(
                                    'id_ligne'=>$lignecp->getId(),
                                    'numero_ligne'=>$lignecp->getNumeroArbrecp(),
                                    'essence'=>$essence,
                                    'x_arbre'=>$lignecp->getXArbrecp(),
                                    'y_arbre'=>$lignecp->getYArbrecp(),
                                    'zh_arbre'=>$zh,
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
                                    'fut_abandon'=>$lignecp->isFutAbandon(),
                                    'exploitant'=>$attribution->getCodeExploitant()->getSigle(),
                                    'cantonnement'=>$attribution->getCodeForet()->getCodeCantonnement()->getNomCantonnement(),
                                    'dr'=>$attribution->getCodeForet()->getCodeCantonnement()->getCodeDr()->getDenomination()

                                );
                            }
                        }
                    }
                }
            }


            //CP
            $attributions = $registry->getRepository(Attribution::class)->findBy(['code_exploitant'=>$user->getCodeexploitant()]);
            foreach($attributions as $attrib){
                $reprises  = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attrib, 'exercice'=>$exercice]);
                foreach($reprises as $reprise){
                    //$reprises_count = $reprises_count +1;
                    $doccps  = $registry->getRepository(Documentcp::class)->findBy(['code_reprise'=>$reprise]);
                    foreach($doccps as $doccp){
                        $nb_cp_op = $nb_cp_op + 1;
                        $vol = 0;
                    }
                }
            }

            //Liste Essences exploitées

            $attributions = $registry->getRepository(Attribution::class)->findBy(['code_exploitant'=>$user->getCodeexploitant()]);
            foreach($attributions as $attrib){
                $reprises  = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attrib, 'exercice'=>$exercice]);
                foreach($reprises as $reprise){
                    //$reprises_count = $reprises_count +1;
                    $docbrhs  = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$reprise]);
                    foreach($docbrhs as $docbrh){
                        $nb_brh_op = $nb_brh_op + 1;
                        $vol = 0;
                        $pagebrhs  = $registry->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$docbrh]);
                        foreach($pagebrhs as $page){
                            $lignepagebrhs  = $registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$page]);

                            foreach($lignepagebrhs as $lignebrh){
                                if ($lignebrh->getNomEssencebrh()){
                                    $liste_essences_exploitees[] = array(
                                        'id_essence'=>$lignebrh->getNomEssencebrh()->getId(),
                                        'nom_essence'=>$lignebrh->getNomEssencebrh()->getNomVernaculaire(),
                                        'cubage'=>$lignebrh->getCubageLignepagebrh()
                                    );
                                }

                                $vol = $vol + $lignebrh->getCubageLignepagebrh();
                            }
                        }
                        $liste_doc_brh[] = array(
                            'id_brh'=>$docbrh->getId(),
                            'numero_brh'=>$docbrh->getNumeroDocbrh(),
                            'volume'=>$vol
                        );
                    }
                }
            }

            $essences = $registry->getRepository(Essence::class)->findAll();
            foreach ($essences as $essence){
                $vol = 0;
                for ($i=0; $i < count($liste_essences_exploitees); $i++){
                    if ((int)$liste_essences_exploitees[$i]['id_essence'] == $essence->getId()){
                        $vol = $vol + (float)$liste_essences_exploitees[$i]['cubage'];
                    }
                }

                if ($vol > 0) {
                    $liste_essences_vol[] = array(
                        'vol'=>$vol,
                        'nom_vernaculaire'=>$essence->getNomVernaculaire()
                    );
                }
            }

            arsort($liste_essences_vol);


        }

        // Interface Industriel
        if ($this->isGranted('ROLE_INDUSTRIEL')){

            // Disponibilité des billes sur le parc chantier;
            $dispoBilles = $registry->getRepository(DisponibiliteParcBilles::class)->findBy([
                'code_usine'=>$user->getCodeindustriel()->getId()
            ]);
            foreach ($dispoBilles as $bille){

                    $essence_disponibles[] = array(
                        'essence'=>$bille->getNomVernaculaire(),
                        'nb'=>$bille->getNbBilles(),
                        'volume'=>$bille->getVolume()
                    );
            }

            sort($essence_disponibles);

            // Disponibilité des billons sur le parc chantier;
            $dispoBillons = $registry->getRepository(DisponibiliteParcBillons::class)->findBy([
                'code_usine'=>$user->getCodeindustriel()->getId()
            ]);
            foreach ($dispoBillons as $bille){

                $billons_disponibles[] = array(
                    'essence'=>$bille->getNomVernaculaire(),
                    'nb'=>$bille->getNbBillons(),
                    'volume'=>$bille->getVolume()
                );
            }

            sort($billons_disponibles);
        }



        $lignecp = $registry->getRepository(Lignepagecp::class)->findBy(['code_exercice'=>$exercice]);
        $userss = $registry->getRepository(User::class)->findAll();
        foreach($userss as $user_admin){
            $this->snvlt1->Change_Exploitant($user_admin);
        }


        //dd($registry->getRepository(PublicationRapport::class)->findByStatut());
        return $this->render('tdb_admin/index.html.twig',
            [
                'rapport_oi'=>$registry->getRepository(PublicationRapport::class)->findByStatut(),
                'tickets_oi'=>$registry->getRepository(Ticket::class)->findBy([],['id'=>'DESC'],5,0),
                'operateurs'=>$registry->getRepository(TypeOperateur::class)->findAll(),
                'type_rapports_oi'=>$registry->getRepository(PublicationRapport::class)->findAll(),
                'chrs' => $page_brh,
                //'documents'=>$liste_documents,
                //'log_snvlt'=>$logsnvlt,
                'liste_menus'=>$menus->findOnlyParent(),
                'liste_parent'=>$permissions,
                "all_menus"=>$menus->findAll(),
                'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],['created_at'=>'DESC'],5,0),
                'groupe'=>$code_groupe,
                'arbres'=>$lignecp,
                'essences_vol'=>array_slice($liste_essences_vol, 0, 10),
                //'essences_admin'=>array_slice($liste_essences_admin, 0, 10),
                'doc_brh_op'=>array_slice($liste_doc_brh, 0, 3),
                'quotas'=>$liste_quotas,
                'nb_reprises'=>$reprises_count,
                'nb_doc_brh_op'=>$nb_brh_op,
                'nb_doc_cp_op'=>$nb_cp_op,
                'point_ddef'=>$point_ddef,
                'drs'=>$registry->getRepository(Dr::class)->findAll(),

                'suivi_saisies'=>$registry->getRepository(PerformanceBrhJour::class)->findBy([],['created_at'=>'ASC']),
                'arbres_abattus'=>$arbres_abattus,
                'vol_brh'=>round($volume_brh,3),
                'nb_utilisateurs'=>$nb_utilisateurs,
                'forets_cef'=>$volume_foret,
                'chr_cef'=>$chargements_cef,
                'essences_dispo'=>$essence_disponibles,
                'billons_dispo'=>$billons_disponibles,
                'parc_usine_stats'=>array_slice($liste_billes_industriel, 0, 10),
                'exercice'=>$exercice,
                'users'=>$registry->getRepository(User::class)->findAll()
            ]);
    }
    #[Route('/snvlt/admin/cubage_exploitation', name: 'cubage_exploitation')]
    public function cubage_exploitation(
        Request $request,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            $array_exploitation = array();
            $total_exploitation = 0;
            $exercice = $this->administrationService->getAnnee();
            $total_volume = $registry->getRepository(Lignepagebrh::class)->findBy(['exercice'=>$exercice]);
            foreach ($total_volume as $ligne){
                $total_exploitation = $total_exploitation + $ligne->getCubageLignepagebrh();
            }
            $array_exploitation[] = array(
                'nb'=>$total_exploitation
            );
            return new JsonResponse(json_encode($array_exploitation));
        }

    }
    #[Route('/snvlt/docbrh/brh_cubagetotal', name: 'brh_cubagetotal')]
    public function brh_cubagetotal(
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
                $total_transformation= 0;

                $billes = $registry->getRepository(Lignepagebrh::class)->findBy(['exercice'=>$this->administrationService->getAnnee()]);
                $billes_bcbgfh = $registry->getRepository(Lignepagebcbgfh::class)->findBy(['exercice'=>$this->administrationService->getAnnee()]);
                foreach ($billes as $bille){
                    $cubage = $cubage + $bille->getCubageLignepagebrh();
                }
                foreach ($billes_bcbgfh as $bille){
                    $cubage = $cubage + $bille->getCubageLignepagebcbgfh();
                }



                $response[] = array(
                    'cubage'=>round($cubage, 0),
                    'transfo'=>round($total_transformation, 0),
                );


                return  new JsonResponse(json_encode($response));



            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/docbrh/chargements_en_cours', name: 'chargements_en_cours')]
    public function chargements_en_cours(
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
                // Chargements en cours
                $liste_chargements = array();
                $exercice = $this->administrationService->getAnnee();
                $docs_brh = $registry->getRepository(ChargementCours::class)->findBy(['exo'=>$exercice->getId()]);
                foreach ($docs_brh as $doc_brh){
                        $liste_chargements[] = array(
                            'date_chargement'=>$doc_brh->getDateChargement()->format('d/m/Y'),
                            'numero_chargement'=>$doc_brh->getNumeroPagebrh(),
                            'volume'=>round($doc_brh->getVolume(), 3),
                            'expediteur'=>$doc_brh->getRsExploitant(),
                            'immatriculation'=>$doc_brh->getImmatcamion(),
                            'destinataire'=>$doc_brh->getRsUsine()
                        );
                    }



                //rsort($liste_chargements);


                return  new JsonResponse(json_encode($liste_chargements));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/admin/nb_users', name: 'nb_uusers')]
    public function nb_users(
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

                $nombre_users = array();
                //Nombre d'utilisateurs
                $nb_users_all = $registry->getRepository(User::class)->findAll();
                $nb_users = 0;
                foreach ($nb_users_all as $nb){
                    $nb_users = $nb_users + 1;
                }

                $nombre_users[] = array(
                    'nb'=>$nb_users
                );

                //rsort($liste_chargements);


                return  new JsonResponse(json_encode($nombre_users));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/admin/essences_volumes', name: 'essences_volumes')]
    public function essences_volumes(
        Request $request,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN'))
            {

                $exercice = $this->administrationService->getAnnee();
                // essences et cubages
                $liste_essences_admin = array();
                $liste_essences = $registry->getRepository(Essence::class)->findAll();

                foreach($liste_essences as $ess){
                    $linge_brh_essences_admin = $registry->getRepository(Lignepagebrh::class)->findBy(['nom_essencebrh'=>$ess, 'exercice'=>$exercice]);
                    $vol_ess_brh = 0;
                    foreach($linge_brh_essences_admin as $essence_brh){
                        $vol_ess_brh = $vol_ess_brh + $essence_brh->getCubageLignepagebrh();
                    }
                    $liste_essences_admin[] = array(
                        'volume'=>round($vol_ess_brh,0),
                        'essence'=>$ess->getNomVernaculaire()
                    );
                }
                arsort($liste_essences_admin);


                return  new JsonResponse(json_encode(array_slice($liste_essences_admin, 0, 10),));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/admin/chr_usine/{id_usine}', name: 'chr_usines')]
    public function chr_usines(
        int $id_usine,
        Request $request,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_INDUSTRIEL'))
            {

                $liste_chr_industriel = array();
                $usine = $registry->getRepository(Usine::class)->find($id_usine);
                if ($usine){
                    // Chargement des BRH
                    $chrs_usine = $registry->getRepository(Pagebrh::class)->findBy([
                        'parc_usine_brh'=>$usine,
                        'confirmation_usine'=>false,
                        'fini'=>true
                    ]);
                    foreach ($chrs_usine as $chr){
                        $exp = $chr->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getRaisonSocialeExploitant();
                        if ($chr->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getSigle()){
                            $exp = $chr->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getSigle();
                        }

                        $nb_billes = 0;
                        $volume_billes = 0;
                        $ess = "-";

                        $billes_chr = $registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$chr]);
                        foreach ($billes_chr as $bille){
                            $nb_billes = $nb_billes + 1;
                            $volume_billes = $volume_billes + $bille->getCubageLignepagebrh();
                        }
                        foreach ($registry->getRepository(Essence::class)->findAll() as $essence){
                            foreach ($billes_chr as $bille){
								if ($bille->getNomEssencebrh()){
									if ($essence->getId() == $bille->getNomEssencebrh()->getId()){
										$ess = $ess . " - " . $bille->getNomEssencebrh()->getNomVernaculaire();
										break;
									}
								}
                            }
                        }
                        if ($registry->getRepository(TypeDocumentStatistique::class)->find(2)){
                            $doc_abv = $registry->getRepository(TypeDocumentStatistique::class)->find(2)->getAbv();
                            $id_doc_abv = $registry->getRepository(TypeDocumentStatistique::class)->find(2)->getId();
                        } else {
                            $doc_abv = "SOURCE_INCONNUE";
                            $id_doc_abv = 0;
                        }

                        $liste_chr_industriel[] = array(
                            'date_chr'=>$chr->getDateChargementbrh()->format('d/m/Y'),
                            'id'=>$chr->getId(),
                            'numero'=>$chr->getNumeroPagebrh(),
                            'immat'=>$chr->getImmatcamion(),
                            'foret'=>$chr->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination(),
                            'document'=>$chr->getCodeDocbrh()->getNumeroDocbrh(),
                            'exploitant'=>$exp,
                            'essences'=>$ess,
                            'nb_billes'=>$nb_billes,
                            'volume'=>round($volume_billes,3),
                            'source'=>$doc_abv,
                            'id_source'=>$id_doc_abv
                        );
                    }

                    // Chargement des BCBP
                    $chrs_bcbp_usine = $registry->getRepository(Pagebcbp::class)->findBy([
                        'parc_usine'=>$usine,
                        'confirmation_usine'=>false,
                        'fini'=>true
                    ]);
                    foreach ($chrs_bcbp_usine as $chr){

                        if ($chr->getCodeDocbcbp()->getCodeAutorisationPv()->getCodeExploitant()->getSigle()){
                            $exp = $chr->getCodeDocbcbp()->getCodeAutorisationPv()->getCodeExploitant()->getSigle();
                        } else {
                            $exp = $chr->getCodeDocbcbp()->getCodeAutorisationPv()->getCodeExploitant()->getRaisonSocialeExploitant();
                        }

                        $nb_billes = 0;
                        $volume_billes = 0;
                        if ($chr->getEssence()->getNomVernaculaire()){
                            $ess = $chr->getEssence()->getNomVernaculaire();
                        } else {
                            $ess = '-';
                        }


                        $billes_chr = $registry->getRepository(Lignepagebcbp::class)->findBy(['code_pagebcbp'=>$chr]);
                        foreach ($billes_chr as $bille){
                            $nb_billes = $nb_billes + 1;
                            $volume_billes = $volume_billes + $bille->getVolume();
                        }

                        if ($registry->getRepository(TypeDocumentStatistique::class)->find(3)){
                            $doc_abv = $registry->getRepository(TypeDocumentStatistique::class)->find(3)->getAbv();
                            $id_doc_abv = $registry->getRepository(TypeDocumentStatistique::class)->find(3)->getId();
                        } else {

                            $doc_abv = "SOURCE_INCONNUE";
                            $id_doc_abv = 0;
                        }

                        $liste_chr_industriel[] = array(
                            'date_chr'=>$chr->getDateChargement()->format('d/m/Y'),
                            'id'=>$chr->getId(),
                            'numero'=>$chr->getNumeroPagebcbp(),
                            'immat'=>$chr->getImmatcamion(),
                            'foret'=>$chr->getCodeDocbcbp()->getCodeAutorisationPv()->getCodeAttributionPv()->getCodeParcelle()->getDenomination(),
                            'document'=>$chr->getCodeDocbcbp()->getNumeroDocbcbp(),
                            'exploitant'=>$exp,
                            'essences'=>$ess,
                            'nb_billes'=>$nb_billes,
                            'volume'=>round($volume_billes,3),
                            'source'=>$doc_abv,
                            'id_source'=>$id_doc_abv
                        );
                    }
                    rsort($liste_chr_industriel);
                }

                return  new JsonResponse(json_encode($liste_chr_industriel));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
    #[Route('/snvlt/admin/stats_auto', name: 'stats_auto')]
    public function stats_auto(
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

                $nb_autorisations = 0;
                $total_agreements = 0;
                $total_transformation = 0;

                $stats_autorisations = array();
                $exercice = $this->administrationService->getAnnee();

                // Agreement
                $pef = 0;
                $pv = 0;
                $ps = 0;
                $export = 0;
                foreach ($registry->getRepository(Attribution::class)->findBy(['statut'=>true, 'exercice'=>$exercice]) as $att){
                    $pef = $pef + 1;
                }
                foreach ($registry->getRepository(AttributionPv::class)->findAll() as $att){
                    $pv = $pv + 1;
                }
                foreach ($registry->getRepository(AgreementPs::class)->findAll() as $att){
                    $ps = $ps + 1;
                }
                foreach ($registry->getRepository(AgreementExportateur::class)->findAll() as $att){
                    $export = $export + 1;
                }
                $total_agreements  =$pef + $pv + $ps + $export;


                // Autorisations
                $pef_auto = 0;
                $pv_auto = 0;
                $ps_auto = 0;
                $export_auto = 0;
                foreach ($registry->getRepository(Reprise::class)->findBy(['statut'=>true,'exercice'=>$exercice]) as $att){
                    $pef_auto = $pef_auto + 1;
                }
                foreach ($registry->getRepository(AutorisationPv::class)->findBy(['exercice'=>$exercice]) as $att){
                    $pv_auto = $pv_auto + 1;
                }
                foreach ($registry->getRepository(AutorisationPs::class)->findBy(['exercice'=>$exercice]) as $att){
                    $ps_auto = $ps_auto + 1;
                }
                foreach ($registry->getRepository(AutorisationExportateur::class)->findBy(['exercice'=>$exercice]) as $att){
                    $export_auto = $export_auto + 1;
                }
                $total_autorisations  =$pef_auto + $pv_auto + $ps_auto + $export_auto;


                // Nombre Users
                //Nombre d'utilisateurs
                $nb_users_all = $registry->getRepository(User::class)->findAll();
                $nb_users = 0;
                foreach ($nb_users_all as $nb){
                    $nb_users = $nb_users + 1;
                }

                //Transformation en m3
                $total_transformation = 0;

                $total_volume_billon = $registry->getRepository(Billon::class)->findAll();
                foreach ($total_volume_billon as $billon){
                    $total_transformation = $total_transformation + $billon->getVolume();
                }

                $cubage= 0;

                $billes = $registry->getRepository(Lignepagebrh::class)->findBy(['exercice'=>$this->administrationService->getAnnee()]);
                $billes_bcbgfh = $registry->getRepository(Lignepagebcbgfh::class)->findBy(['exercice'=>$this->administrationService->getAnnee()]);
                foreach ($billes as $bille){
                    $cubage = $cubage + $bille->getCubageLignepagebrh();
                }
                foreach ($billes_bcbgfh as $bille){
                    $cubage = $cubage + $bille->getCubageLignepagebcbgfh();
                }



                $stats_autorisations[] = array(
                    'nb_auto'=>$total_autorisations,
                    'nb_agrements'=>$total_agreements,
                    'nb_users'=>$nb_users,
                    'transfo'=>round($total_transformation, 0),
                    'cubage_exploitation'=>round($cubage, 0),
                );





                return  new JsonResponse(json_encode($stats_autorisations));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/admin/docstats', name: 'docstats')]
    public function docstats(
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

                $liste_documents= array();
                $exercice = $this->administrationService->getAnnee();
                $typedocs = $registry->getRepository(TypeDocumentStatistique::class)->findBy(['statut'=>'ACTIF']);

                foreach ($typedocs as $typedoc){
                    $nb_docs = 0;
                    if ($typedoc->getId() == 1){
                        $mes_doccps = $registry->getRepository(Documentcp::class)->findBy(['exercice'=>$exercice]);
                        foreach ($mes_doccps as $doccp) {
                            $nb_docs = $nb_docs + 1;
                        }
                    } elseif ($typedoc->getId() == 2){
                        $mes_docbrhs = $registry->getRepository(Documentbrh::class)->findBy(['exercice'=>$exercice]);
                        foreach ($mes_docbrhs as $docbrh) {
                            $nb_docs = $nb_docs + 1;
                        }
                    } elseif ($typedoc->getId() == 3){
                        $mes_docbcbps = $registry->getRepository(Documentbcbp::class)->findBy(['exercice'=>$exercice]);
                        foreach ($mes_docbcbps as $mes_docbcbp) {
                            $nb_docs = $nb_docs + 1;
                        }
                    } elseif ($typedoc->getId() == 4){
                        $mes_docebs = $registry->getRepository(Documentetatb::class)->findBy(['exercice'=>$exercice]);
                        foreach ($mes_docebs as $mes_doceb) {
                            $nb_docs = $nb_docs + 1;
                        }
                    } elseif ($typedoc->getId() == 5){
                        $mes_docljes = $registry->getRepository(Documentlje::class)->findBy(['exercice'=>$exercice]);
                        foreach ($mes_docljes as $mes_doclje) {
                            $nb_docs = $nb_docs + 1;
                        }
                    } elseif ($typedoc->getId() == 6){
                        $mes_docbtgus = $registry->getRepository(Documentbtgu::class)->findBy(['exercice'=>$exercice]);
                        foreach ($mes_docbtgus as $mes_docbtgu) {
                            $nb_docs = $nb_docs + 1;
                        }
                    } elseif ($typedoc->getId() == 7){
                        $mes_docfps = $registry->getRepository(Documentfp::class)->findBy(['exercice'=>$exercice]);
                        foreach ($mes_docfps as $mes_docfp) {
                            $nb_docs = $nb_docs + 1;
                        }
                    } elseif ($typedoc->getId() == 8){
                        $mes_docetates = $registry->getRepository(Documentetate::class)->findBy(['exercice'=>$exercice]);
                        foreach ($mes_docetates as $mes_docetate) {
                            $nb_docs = $nb_docs + 1;
                        }
                    } elseif ($typedoc->getId() == 9){
                        $mes_docetate2s = $registry->getRepository(Documentetate2::class)->findBy(['exercice'=>$exercice]);
                        foreach ($mes_docetate2s as $mes_docetate2) {
                            $nb_docs = $nb_docs + 1;
                        }
                    } elseif ($typedoc->getId() == 10){
                        $mes_docetatgs = $registry->getRepository(Documentetatg::class)->findBy(['exercice'=>$exercice]);
                        foreach ($mes_docetatgs as $mes_docetatg) {
                            $nb_docs = $nb_docs + 1;
                        }
                    } elseif ($typedoc->getId() == 11){
                        $mes_docetaths = $registry->getRepository(Documentetath::class)->findBy(['exercice'=>$exercice]);
                        foreach ($mes_docetaths as $mes_docetath) {
                            $nb_docs = $nb_docs + 1;
                        }
                    } elseif ($typedoc->getId() == 12){
                        $mes_docdmps = $registry->getRepository(Documentdmp::class)->findBy(['exercice'=>$exercice]);
                        foreach ($mes_docdmps as $mes_docdmp) {
                            $nb_docs = $nb_docs + 1;
                        }
                    } elseif ($typedoc->getId() == 13){
                        $mes_docdmvs = $registry->getRepository(Documentdmv::class)->findBy(['exercice'=>$exercice]);
                        foreach ($mes_docdmvs as $mes_docdmv) {
                            $nb_docs = $nb_docs + 1;
                        }
                    } elseif ($typedoc->getId() == 15){
                        $mes_docpdtdrvs = $registry->getRepository(Documentpdtdrv::class)->findBy(['exercice'=>$exercice]);
                        foreach ($mes_docpdtdrvs as $mes_docpdtdrv) {
                            $nb_docs = $nb_docs + 1;
                        }
                    } elseif ($typedoc->getId() == 18){
                        $mes_docbcburbs = $registry->getRepository(Documentbcburb::class)->findBy(['exercice'=>$exercice]);
                        foreach ($mes_docbcburbs as $mes_docbcburb) {
                            $nb_docs = $nb_docs + 1;
                        }
                    } elseif ($typedoc->getId() == 19){
                        $mes_docbrepfs = $registry->getRepository(Documentbrepf::class)->findBy(['exercice'=>$exercice]);
                        foreach ($mes_docbrepfs as $docbrepf) {
                            $nb_docs = $nb_docs + 1;
                        }
                    } elseif ($typedoc->getId() == 20){
                        $mes_docrsdpfs = $registry->getRepository(Documentrsdpf::class)->findBy(['exercice'=>$exercice]);
                        foreach ($mes_docrsdpfs as $mes_docrsdpf) {
                            $nb_docs = $nb_docs + 1;
                        }
                    } elseif ($typedoc->getId() == 21){
                        $mes_docbcbgfhs = $registry->getRepository(Documentbcbgfh::class)->findBy(['exercice'=>$exercice]);
                        foreach ($mes_docbcbgfhs as $mes_docbcbgfh) {
                            $nb_docs = $nb_docs + 1;
                        }
                    }
                    $liste_documents[] = array(
                        'document'=>$typedoc->getAbv(),
                        'nb'=>$nb_docs
                    );
                }
                return  new JsonResponse(json_encode($liste_documents));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
    #[Route('/snvlt/dr/PointDr/{id_dr?0}', name: 'point_dr')]
    public function point_dr(
        Request $request,
        int $id_dr,
        UserRepository $userRepository,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DR') or $this->isGranted('ROLE_MINEF'))
            {
                $dr = $registry->getRepository(Dr::class)->find($id_dr);
                $point_dr = array();
                $exercice = $this->administrationService->getAnnee();
                if ($dr) {

                    $cantons = $registry->getRepository(Cantonnement::class)->findBy(['code_dr'=>$dr]);
                    foreach ($cantons as $canton ){
                        $forets = $canton->getForets();
                        foreach ($forets as $foret){
                            $exploitant = "-";
                            $nb_cp = 0;
                            $nb_brh = 0;
                            $arbre_abattus = 0;
                            $volume_abattage = 0;
                            $volume_brh = 0;
                            $decision_reprise = "-";
                            $decision_attribution = "-";

                            $attributions = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$foret, 'statut'=>true]);
                            foreach($attributions as $attribution){

                                if ($attribution->getCodeExploitant()->getSigle()){
                                    $exploitant = $attribution->getCodeExploitant()->getSigle();
                                } else {
                                    $exploitant = $attribution->getCodeExploitant()->getRaisonSocialeExploitant();
                                }

                                $decision_attribution = $attribution->getNumeroDecision(). " du ". $attribution->getDateDecision()->format('d/m/Y');
                                $rep = $registry->getRepository(Reprise::class)->findOneBy(['code_attribution'=>$attribution, 'exercice'=>$exercice]);

                                if ($rep){
                                    $decision_reprise = $rep->getNumeroAutorisation(). " du ". $rep->getDateAutorisation()->format('d/m/Y');
                                }

                                $doc_cps = $registry->getRepository(Documentcp::class)->findBy(['code_reprise'=>$rep]);
                                $doc_brhs = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$rep]);

                                foreach ($doc_cps as $doc_cp){
                                    $nb_cp = $nb_cp + 1;
                                    $arbres_abattus = $nb_cp;
                                    $pagescp = $registry->getRepository(Pagecp::class)->findBy(['code_doccp'=>$doc_cp]);
                                    foreach ($pagescp as $page){
                                        $lignecps = $registry->getRepository(Lignepagecp::class)->findBy(['code_pagecp'=>$page]);
                                        foreach($lignecps as $lignecp){
                                            $volume_abattage = $volume_abattage + $lignecp->getVolumeArbrecp();
                                            $arbre_abattus = $arbre_abattus + 1;
                                        }
                                    }
                                }

                                foreach ($doc_brhs as $doc_brh){
                                    $nb_brh = $nb_brh + 1;
                                    $pagesbrh = $registry->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$doc_brh]);
                                    foreach ($pagesbrh as $page){
                                        $lignebrhs = $registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$page]);
                                        foreach($lignebrhs as $lignebrh){
                                            $volume_brh = $volume_brh + $lignebrh->getCubageLignepagebrh();
                                        }
                                    }
                                }

                                $point_dr[] = array(
                                    'info'=>'SUCCESS',
                                    'foret'=>$foret->getDenomination(),
                                    'cantonnement'=>$canton,
                                    'exploitant'=>$exploitant,
                                    'decision_attribution'=>$decision_attribution,
                                    'decision_reprise'=>$decision_reprise,
                                    'nb_cp'=>$nb_cp,
                                    'nb_brh'=>$nb_brh,
                                    'nb_arbres_abattus'=>$arbre_abattus,
                                    'volume_abattage'=>round($volume_abattage,3),
                                    'volume_brh'=>round($volume_brh,3)
                                );

                            }
                        }
                    }
                } else {
                    $point_dr[] = array(
                        'info'=>'ERROR'
                    );
                }
                    return  new JsonResponse(json_encode($point_dr));
                } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
}
