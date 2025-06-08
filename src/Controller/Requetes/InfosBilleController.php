<?php

namespace App\Controller\Requetes;

use App\Controller\Services\AdministrationService;
use App\Entity\Autorisation\Attribution;
use App\Entity\Autorisation\Reprise;
use App\Entity\DocStats\Entetes\Documentbrh;
use App\Entity\DocStats\Entetes\Documentlje;
use App\Entity\DocStats\Pages\Pagebrh;
use App\Entity\DocStats\Pages\Pagelje;
use App\Entity\DocStats\Saisie\Lignepagebrh;
use App\Entity\DocStats\Saisie\Lignepagelje;
use App\Entity\References\Cantonnement;
use App\Entity\References\Foret;
use App\Entity\User;
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

class InfosBilleController extends AbstractController
{
    public function __construct(private AdministrationService $administrationService)
    {
    }

    #[Route('snvlt/requetes/infos/bille', name: 'infos_bille')]
    public function index(
        Request $request,
        UserRepository $userRepository,
        ManagerRegistry $registry,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        User $user = null,
        NotificationRepository $notification
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or
                $this->isGranted('ROLE_INDUSTRIEL') or
                $this->isGranted('ROLE_ADMINISTRATIF') or
                $this->isGranted('ROLE_MINEF') or
                $this->isGranted('ROLE_ADMIN')
            ) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();



                return $this->render('requetes/infos_bille/index.html.twig', [
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'liste_parent'=>$permissions,
                    'exercice'=>$this->administrationService->getAnnee()->getAnnee()
                ]);

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }

    #[Route('snvlt/rechercher_source/bille/{numero_bille}', name: 'recherche_concessions')]
    public function recherche_concessions(
        Request $request,
        UserRepository $userRepository,
        ManagerRegistry $registry,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        User $user = null,
        string $numero_bille,
        NotificationRepository $notification
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or
                $this->isGranted('ROLE_INDUSTRIEL') or
                $this->isGranted('ROLE_ADMINISTRATIF') or
                $this->isGranted('ROLE_MINEF') or
                $this->isGranted('ROLE_ADMIN')
            ) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();


                $liste_concessions = array();


                $numero = substr(strtoupper($numero_bille), 0, -1) ;
                $lettre = substr(strtoupper($numero_bille), -1);

                if ($this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')){
                    $billes = $registry->getRepository(Lignepagebrh::class)->findBy(['numero_lignepagebrh'=>$numero, 'lettre_lignepagebrh'=>$lettre]);
                    foreach ($billes as $bille){
                        $liste_concessions[] = array(
                            'id_source'=>$bille->getCodePagebrh()->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()->getId(),
                            'denomination'=>$bille->getCodePagebrh()->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()->getNumeroForet()
                        );
                    }
                } elseif ($user->getCodeDr()){
                    $cefs = $registry->getRepository(Cantonnement::class)->findBy(['code_dr'=>$user->getCodeDr()]);
                        foreach ($cefs as $cef){
                            $forets = $registry->getRepository(Foret::class)->findBy(['code_cantonnement'=>$cef]);
                            foreach ($forets as $foret){
                                $attrs = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$foret]);
                                foreach ($attrs as $attr){
                                    $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attr]);
                                    foreach ($reprises as $repris){
                                        $documents = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$repris]);
                                        foreach ($documents as $doc){
                                            $pages = $registry->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$doc]);
                                            foreach ($pages as $page){
                                                $billes = $registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$page, 'numero_lignepagebrh'=>$numero, 'lettre_lignepagebrh'=>$lettre]);
                                                foreach ($billes as $bille){
                                                    $liste_concessions[] = array(
                                                        'id_source'=>$attr->getCodeForet()->getId(),
                                                        'denomination'=>$attr->getCodeForet()->getNumeroForet()
                                                    );
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                } elseif ($user->getCodeDdef()){
                    $cefs = $registry->getRepository(Cantonnement::class)->findBy(['code_ddef'=>$user->getCodeDdef()]);
                    foreach ($cefs as $cef){
                        $forets = $registry->getRepository(Foret::class)->findBy(['code_cantonnement'=>$cef]);
                        foreach ($forets as $foret){
                            $attrs = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$foret]);
                            foreach ($attrs as $attr){
                                $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attr]);
                                foreach ($reprises as $repris){
                                    $documents = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$repris]);
                                    foreach ($documents as $doc){
                                        $pages = $registry->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$doc]);
                                        foreach ($pages as $page){
                                            $billes = $registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$page, 'numero_lignepagebrh'=>$numero, 'lettre_lignepagebrh'=>$lettre]);
                                            foreach ($billes as $bille){
                                                $liste_concessions[] = array(
                                                    'id_source'=>$attr->getCodeForet()->getId(),
                                                    'denomination'=>$attr->getCodeForet()->getNumeroForet()
                                                );
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                } elseif ($user->getCodeCantonnement()){

                    $forets = $registry->getRepository(Foret::class)->findBy(['code_cantonnement'=>$user->getCodeCantonnement()]);
                    foreach ($forets as $foret){
                        $attrs = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$foret]);
                        foreach ($attrs as $attr){
                            $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attr]);
                            foreach ($reprises as $repris){
                                $documents = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$repris]);
                                foreach ($documents as $doc){
                                    $pages = $registry->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$doc]);
                                    foreach ($pages as $page){
                                        $billes = $registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$page, 'numero_lignepagebrh'=>$numero, 'lettre_lignepagebrh'=>$lettre]);
                                        foreach ($billes as $bille){
                                            $liste_concessions[] = array(
                                                'id_source'=>$attr->getCodeForet()->getId(),
                                                'denomination'=>$attr->getCodeForet()->getNumeroForet()
                                            );
                                        }
                                    }
                                }
                            }
                        }
                    }
                } elseif ($user->getCodePosteControle()){

                    $forets = $registry->getRepository(Foret::class)->findBy(['code_cantonnement'=>$user->getCodePosteControle()->getCodeCantonnement()]);
                    foreach ($forets as $foret){
                        $attrs = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$foret]);
                        foreach ($attrs as $attr){
                            $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attr]);
                            foreach ($reprises as $repris){
                                $documents = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$repris]);
                                foreach ($documents as $doc){
                                    $pages = $registry->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$doc]);
                                    foreach ($pages as $page){
                                        $billes = $registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$page, 'numero_lignepagebrh'=>$numero, 'lettre_lignepagebrh'=>$lettre]);
                                        foreach ($billes as $bille){
                                            $liste_concessions[] = array(
                                                'id_source'=>$attr->getCodeForet()->getId(),
                                                'denomination'=>$attr->getCodeForet()->getNumeroForet()
                                            );
                                        }
                                    }
                                }
                            }
                        }
                    }

                } elseif ($this->isGranted('ROLE_EXPLOITANT')){
                    $attributions = $registry->getRepository(Attribution::class)->findBy(['code_exploitant'=>$user->getCodeexploitant()]);
                    foreach($attributions as $attribution){
                        $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution]);
                        foreach ($reprises as $reprise) {
                            $documents = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$reprise]);
                            foreach ($documents as $doc){
                                $pages = $registry->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$doc]);
                                foreach ($pages as $page){
                                    $billes = $registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$page, 'numero_lignepagebrh'=>$numero, 'lettre_lignepagebrh'=>$lettre]);
                                    foreach ($billes as $bille){
                                        $liste_concessions[] = array(
                                            'id_source'=>$attribution->getCodeForet()->getId(),
                                            'denomination'=>$attribution->getCodeForet()->getNumeroForet()
                                        );
                                    }
                                }
                            }
                        }
                    }
                } elseif ($this->isGranted('ROLE_INDUSTRIEL')){
                    $document_ljes = $registry->getRepository(Documentlje::class)->findBy([
                        'code_usine'=>$user->getCodeindustriel()
                    ]);
                    foreach ($document_ljes as $lje){
                        $pagesljes = $registry->getRepository(Pagelje::class)->findBy(['code_doclje'=>$lje]);
                        foreach ($pagesljes as $pageslje){
                            $billeslje = $registry->getRepository(Lignepagelje::class)->findBy(['numero_arbre'=>$numero, 'lettre'=>$lettre, 'code_pagelje'=>$pageslje]);
                            foreach ($billeslje as $bille){
                                $att = $registry->getRepository(Pagebrh::class)->find($bille->getCodeFeuillet())
                                    ->getCodeDocbrh()->getCodeReprise()->getCodeAttribution();
                                $liste_concessions[] = array(
                                    'id_source'=>$att->getCodeForet()->getId(),
                                    'denomination'=>$att->getCodeForet()->getNumeroForet()
                                );
                            }
                        }
                    }

                }

                return  new JsonResponse(json_encode($liste_concessions));


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }

    #[Route('snvlt/rechercher_bille/infos/{numero_bille}/{id_source}', name: 'recherche_bille_source')]
    public function recherche_bille_source(
        Request $request,
        UserRepository $userRepository,
        ManagerRegistry $registry,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        User $user = null,
        string $numero_bille,
        int $id_source,
        NotificationRepository $notification
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or
                $this->isGranted('ROLE_INDUSTRIEL') or
                $this->isGranted('ROLE_ADMINISTRATIF') or
                $this->isGranted('ROLE_MINEF') or
                $this->isGranted('ROLE_ADMIN')
            ) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();


                $info_bille = array();


                $numero = substr(strtoupper($numero_bille), 0, -1) ;
                $lettre = substr(strtoupper($numero_bille), -1);

                $foret = $registry->getRepository(Foret::class)->find($id_source);
                if ($foret){
                    $attributions = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$foret]);
                    foreach($attributions as $attribution){
                        $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution]);
                        foreach ($reprises as $reprise) {
                            $documents = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$reprise]);
                            foreach ($documents as $doc){
                                $pages = $registry->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$doc]);
                                foreach ($pages as $page){
                                    $billes = $registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$page, 'numero_lignepagebrh'=>$numero, 'lettre_lignepagebrh'=>$lettre]);
                                    foreach ($billes as $bille){
                                        $fiche_inventaire = "";
                                        $num_arbre_inventaire = "";
                                        $inventaire ="Aucun";
                                        if ($bille->getCodeLigneCp()){
                                            if ($bille->getCodeLigneCp()->getCodeInv()){
                                                if ($bille->getCodeLigneCp()->getCodeInv()->getCodeFicheProspection()){
                                                    $fiche_inventaire = $bille->getCodeLigneCp()->getCodeInv()->getCodeFicheProspection()->getId(). " du ".$bille->getCodeLigneCp()->getCodeInv()->getCodeFicheProspection()->getCreatedAt()->format('d/m/Y') ;
                                                    $num_arbre_inventaire = $bille->getCodeLigneCp()->getCodeInv()->getNumeroArbre();
                                                    $inventaire ="Arbre N° ". $num_arbre_inventaire . " de la fiche de prospection forestière N° " . $fiche_inventaire;
                                                }
                                            }
                                        }
                                        /*declarations variable Transfo et Production */
                                            $doclje = "";
                                            $nb_billons = 0;
                                            $date_dechargement = "";
                                            $tronconnee = false;
                                            $liste_billon = "";
                                        /*Fin déclaration*/

                                        $ligne_lje = $registry->getRepository(Lignepagelje::class)->findOneBy(['code_feuillet'=>$page]);
                                        if($ligne_lje){
                                            $doclje = $ligne_lje->getCodePagelje()->getCodeDoclje()->getNumeroDoclje() . " [page N° ". $ligne_lje->getCodePagelje()->getNumeroPagelje()."]";
                                            $date_dechargement = $ligne_lje->getDateDechargement()->format('d/m/Y');
                                            if ($ligne_lje->isTronconnee()){$tronconnee = true;}
                                            $nb_billons = $ligne_lje->getBillons()->count();
                                            foreach ($ligne_lje->getBillons() as $billon){
                                                $liste_billon = $liste_billon . " - ". $billon->getNumeroBillon();
                                            }
                                        }
                                        $info_bille[] = array(
                                            'id_bille'=>$bille->getId(),
                                            'numero_bille'=>$bille->getNumeroLignepagebrh(). $bille->getLettreLignepagebrh(),
                                            'fiche_inventaire'=>$inventaire,
                                            'arbre_inv'=>$num_arbre_inventaire,
                                            'essence'=>$bille->getNomEssencebrh()->getNomVernaculaire(),
                                            'zh'=>$bille->getZhLignepagebrh()->getZone(),
                                            'x'=>$bille->getXLignepagebrh(),
                                            'y'=>$bille->getYLignepagebrh(),
                                            'lng'=>$bille->getLongeurLignepagebrh(),
                                            'dm'=>$bille->getDiametreLignepagebrh(),
                                            'cubage'=>$bille->getCubageLignepagebrh(),
                                            'operateur'=>$attribution->getCodeExploitant()->getSigle(). " [code :". $attribution->getCodeExploitant()->getNumeroExploitant(). " | Marteau :".$attribution->getCodeExploitant()->getMarteauExploitant() . "]",
                                            'cantonnement'=>$foret->getCodeCantonnement()->getNomCantonnement(),
                                            'dr'=>$foret->getCodeCantonnement()->getCodeDr()->getDenomination(),
                                            'source'=>$foret->getDenomination(),
                                            'chauffeur'=>$page->getChauffeurbrh(),
                                            'immat'=>$page->getImmatcamion(),
                                            'date_transport'=>$page->getDateChargementbrh()->format('d/m/Y'),
                                            'photo'=>$page->getPhoto(),
                                            'destination'=>$page->getDestinationPagebrh(),
                                            'parc_usine'=>$page->getParcUsineBrh()->getSigle(),
                                            'is_lje'=>$page->isEntreLje(),
                                            'lje'=>$doclje,
                                            'date_dechargement'=>$date_dechargement,
                                            'tronconnee'=>$tronconnee,
                                            'nb_billons'=>$nb_billons,
                                            'billons'=>$liste_billon
                                        );
                                    }
                                }
                            }
                        }
                    }
                }




                return  new JsonResponse(json_encode($info_bille));


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }

    #[Route('snvlt/rechercher_billons/infos/{numero_bille}/{id_billon}', name: 'recherche_billons_source')]
    public function recherche_billons_source(
        Request $request,
        UserRepository $userRepository,
        ManagerRegistry $registry,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        User $user = null,
        string $numero_bille,
        int $id_source,
        NotificationRepository $notification
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or
                $this->isGranted('ROLE_INDUSTRIEL') or
                $this->isGranted('ROLE_ADMINISTRATIF') or
                $this->isGranted('ROLE_MINEF') or
                $this->isGranted('ROLE_ADMIN')
            ) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();


                $info_bille = array();


                $billon = substr(strtoupper($numero_bille), 0, -1) ;
                $lettre = substr(strtoupper($numero_bille), -1);

                $foret = $registry->getRepository(Foret::class)->find($id_source);
                if ($foret){
                    $attributions = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$foret]);
                    foreach($attributions as $attribution){
                        $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution]);
                        foreach ($reprises as $reprise) {
                            $documents = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$reprise]);
                            foreach ($documents as $doc){
                                $pages = $registry->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$doc]);
                                foreach ($pages as $page){
                                    $billes = $registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$page, 'numero_lignepagebrh'=>$numero, 'lettre_lignepagebrh'=>$lettre]);
                                    foreach ($billes as $bille){
                                        $fiche_inventaire = "";
                                        $num_arbre_inventaire = "";
                                        $inventaire ="Aucun";
                                        if ($bille->getCodeLigneCp()){
                                            if ($bille->getCodeLigneCp()->getCodeInv()){
                                                if ($bille->getCodeLigneCp()->getCodeInv()->getCodeFicheProspection()){
                                                    $fiche_inventaire = $bille->getCodeLigneCp()->getCodeInv()->getCodeFicheProspection()->getId(). " du ".$bille->getCodeLigneCp()->getCodeInv()->getCodeFicheProspection()->getCreatedAt()->format('d/m/Y') ;
                                                    $num_arbre_inventaire = $bille->getCodeLigneCp()->getCodeInv()->getNumeroArbre();
                                                    $inventaire ="Arbre N° ". $num_arbre_inventaire . " de la fiche de prospection forestière N° " . $fiche_inventaire;
                                                }
                                            }
                                        }
                                        /*declarations variable Transfo et Production */
                                        $doclje = "";
                                        $nb_billons = 0;
                                        $date_dechargement = "";
                                        $tronconnee = false;
                                        $liste_billon = "";
                                        /*Fin déclaration*/

                                        $ligne_lje = $registry->getRepository(Lignepagelje::class)->findOneBy(['code_feuillet'=>$page]);
                                        if($ligne_lje){
                                            $doclje = $ligne_lje->getCodePagelje()->getCodeDoclje()->getNumeroDoclje() . " [page N° ". $ligne_lje->getCodePagelje()->getNumeroPagelje()."]";
                                            $date_dechargement = $ligne_lje->getDateDechargement()->format('d/m/Y');
                                            if ($ligne_lje->isTronconnee()){$tronconnee = true;}
                                            $nb_billons = $ligne_lje->getBillons()->count();
                                            foreach ($ligne_lje->getBillons() as $billon){
                                                $liste_billon = $liste_billon . " - ". $billon->getNumeroBillon();
                                            }
                                        }
                                        $info_bille[] = array(
                                            'id_bille'=>$bille->getId(),
                                            'numero_bille'=>$bille->getNumeroLignepagebrh(). $bille->getLettreLignepagebrh(),
                                            'fiche_inventaire'=>$inventaire,
                                            'arbre_inv'=>$num_arbre_inventaire,
                                            'essence'=>$bille->getNomEssencebrh()->getNomVernaculaire(),
                                            'zh'=>$bille->getZhLignepagebrh()->getZone(),
                                            'x'=>$bille->getXLignepagebrh(),
                                            'y'=>$bille->getYLignepagebrh(),
                                            'lng'=>$bille->getLongeurLignepagebrh(),
                                            'dm'=>$bille->getDiametreLignepagebrh(),
                                            'cubage'=>$bille->getCubageLignepagebrh(),
                                            'operateur'=>$attribution->getCodeExploitant()->getSigle(). " [code :". $attribution->getCodeExploitant()->getNumeroExploitant(). " | Marteau :".$attribution->getCodeExploitant()->getMarteauExploitant() . "]",
                                            'cantonnement'=>$foret->getCodeCantonnement()->getNomCantonnement(),
                                            'dr'=>$foret->getCodeCantonnement()->getCodeDr()->getDenomination(),
                                            'source'=>$foret->getDenomination(),
                                            'chauffeur'=>$page->getChauffeurbrh(),
                                            'immat'=>$page->getImmatcamion(),
                                            'date_transport'=>$page->getDateChargementbrh()->format('d/m/Y'),
                                            'photo'=>$page->getPhoto(),
                                            'destination'=>$page->getDestinationPagebrh(),
                                            'parc_usine'=>$page->getParcUsineBrh()->getSigle(),
                                            'is_lje'=>$page->isEntreLje(),
                                            'lje'=>$doclje,
                                            'date_dechargement'=>$date_dechargement,
                                            'tronconnee'=>$tronconnee,
                                            'nb_billons'=>$nb_billons,
                                            'billons'=>$liste_billon
                                        );
                                    }
                                }
                            }
                        }
                    }
                }




                return  new JsonResponse(json_encode($info_bille));


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }
}
