<?php

namespace App\Controller\Requetes;

use App\Controller\Services\AdministrationService;
use App\Entity\Autorisation\Attribution;
use App\Entity\Autorisation\Reprise;
use App\Entity\DocStats\Entetes\Documentbrh;
use App\Entity\DocStats\Pages\Pagebrh;
use App\Entity\DocStats\Saisie\Lignepagebrh;
use App\Entity\DocStats\Saisie\Lignepagelje;
use App\Entity\References\Foret;
use App\Entity\Transformation\Colis;
use App\Entity\Transformation\Contrat;
use App\Entity\Transformation\ElementsColis;
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
use Symfony\Contracts\Translation\TranslatorInterface;

class InfosColisController extends AbstractController
{
    public function __construct(private AdministrationService $administrationService)
    {
    }

    #[Route('snvlt/requetes/infos/colis', name: 'infos_colis')]
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
            if ($this->isGranted('ROLE_INDUSTRIEL') or
                $this->isGranted('ROLE_ADMINISTRATIF') or
                $this->isGranted('ROLE_MINEF') or
                $this->isGranted('ROLE_ADMIN')
            ) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();



                return $this->render('requetes/infos_colis/index.html.twig', [
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

    #[Route('snvlt/rechercher_contrat/colis/{numero_colis}', name: 'recherche_contrat')]
    public function recherche_contrat(
        Request $request,
        UserRepository $userRepository,
        ManagerRegistry $registry,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        User $user = null,
        string $numero_colis,
        NotificationRepository $notification
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL') or
                $this->isGranted('ROLE_ADMINISTRATIF') or
                $this->isGranted('ROLE_MINEF') or
                $this->isGranted('ROLE_ADMIN')
            ) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();


                $liste_contrat = array();



                if ($this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')){
                    $colis = $registry->getRepository(Colis::class)->findBy(['reference'=>$numero_colis]);
                    foreach ($colis as $col){
                        $liste_contrat[] = array(
                            'id_contrat'=>$col->getCodeContrat()->getId(),
                            'reference'=>$col->getCodeContrat()->getNumeroContrat()
                        );
                    }
                } elseif ($this->isGranted('ROLE_INDUSTRIEL')){

                    $contrats = $registry->getRepository(Contrat::class)->findBy(['code_usine'=>$user->getCodeindustriel()]);
                    foreach($contrats as $contrat){
                        $colis = $registry->getRepository(Colis::class)->findBy(['reference'=>$numero_colis, 'code_contrat'=>$contrat]);
                        foreach ($colis as $col){
                            $liste_contrat[] = array(
                                'id_contrat'=>$col->getCodeContrat()->getId(),
                                'reference'=>$col->getCodeContrat()->getNumeroContrat()
                            );
                        }
                    }
                }

                return  new JsonResponse(json_encode($liste_contrat));


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }

    #[Route('snvlt/rechercher_infos_colis/infos/{numero_colis}/{id_contrat}', name: 'rechercher_infos_colis')]
    public function rechercher_infos_colis(
        Request $request,
        UserRepository $userRepository,
        ManagerRegistry $registry,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        User $user = null,
        string $numero_colis,
        int $id_contrat,
        NotificationRepository $notification
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL') or
                $this->isGranted('ROLE_ADMINISTRATIF') or
                $this->isGranted('ROLE_MINEF') or
                $this->isGranted('ROLE_ADMIN')
            ) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();


                $info_colis = array();

                $contrat = $registry->getRepository(Contrat::class)->find($id_contrat);

                if ($contrat){
                    // Rechercher le premier ou unique colis du contrat
                    $colis = $registry->getRepository(Colis::class)->findOneBy(['code_contrat'=>$contrat, 'reference'=>$numero_colis]);
                    if ($colis){
                        //Calcul du volume et du nombre d'éléments
                        $volume_colis = 0;
                        $nombre_elements = 0;
                        $elements = $registry->getRepository(ElementsColis::class)->findBy(['code_colis'=>$colis]);
                        foreach($elements as $element){
                            $volume_colis = $volume_colis + $element->getVolume();
                            $nombre_elements = $nombre_elements + $element->getNombre();
                        }
                        $info_colis[] = array(
                            'id_colis'=>$colis->getId(),
                            'reference'=>$colis->getReference(),
                            'contrat'=>$contrat->getNumeroContrat(),
                            'type_transformation'=>$contrat->getTypeTransfo()->getLibelle(),
                            'type_contrat'=>$contrat->getTypeContrat()->getLibelle(),
                            'essence'=>$colis->getCodeEssence()->getNomVernaculaire(),
                            'etat_hygro'=>$colis->getEtatHygro(),
                            'date_confection'=>$colis->getDateConfection()->format('d/m/Y'),
                            'fournisseur'=>$colis->getCodeContrat()->getCodeUsine()->getSigle(). " - CODE : ". $colis->getCodeContrat()->getCodeUsine()->getNumeroUsine(),
                            'client'=>$colis->getCodeContrat()->getRaisonSocialeClt(),
                            'pays'=>$colis->getCodeContrat()->getPays()->getDenomination(),
                            'Destination'=>$colis->getCodeContrat()->getDestinationColis(). " - " . $colis->getCodeContrat()->getVille(),
                            'volume'=>round($volume_colis, 3),
                            'nb_elts'=>$nombre_elements
                        );
                    }
                }





                return  new JsonResponse(json_encode($info_colis));


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }

    #[Route('snvlt/rechercher_colis/elts/{id_colis}', name: 'elements_colis')]
    public function elements_colis(
        Request $request,
        UserRepository $userRepository,
        ManagerRegistry $registry,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        User $user = null,
        int $id_colis,
        NotificationRepository $notification
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL') or
                $this->isGranted('ROLE_ADMINISTRATIF') or
                $this->isGranted('ROLE_MINEF') or
                $this->isGranted('ROLE_ADMIN')
            ) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $info_elements = array();

                $colis = $registry->getRepository(Colis::class)->find($id_colis);
                if($colis){
                    $elements = $registry->getRepository(ElementsColis::class)->findBy(['code_colis'=>$colis]);

                    foreach($elements as $element){
                        $bille = "";
                        $numero_feuillet = "";
                        $usine_origine = "";
                        $numero_doc = "";
                        $type_doc = "";
                        $foret = "";
                        $region = "";
                        $attributaire = "";
                        $x = 0;
                        $y = 0;

                        $billon = $element->getCodeElements()->getCodeBillon();
                        if($billon->getCodeLignepagelje()->getCodeFeuillet()){
                            $feuillet = $registry->getRepository(Pagebrh::class)->find($billon->getCodeLignepagelje()->getCodeFeuillet());
                            $bille = $billon->getCodeLignepagelje()->getNumeroArbre();
                            $x = $billon->getCodeLignepagelje()->getX();
                            $y = $billon->getCodeLignepagelje()->getY();
                            if ($feuillet){
                                $numero_feuillet = $feuillet->getNumeroPagebrh();
                                $usine_origine = $feuillet->getParcUsineBrh()->getSigle();
                                $numero_doc = $feuillet->getCodeDocbrh()->getNumeroDocbrh();
                                $type_doc = $feuillet->getCodeDocbrh()->getTypeDocument()->getDenomination();
                                $foret = $feuillet->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination();
                                $region = $feuillet->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getNomCantonnement()." [" .$feuillet->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getCodeDr()->getDenomination() . "]" ;
                                $attributaire = $feuillet->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getRaisonSocialeExploitant();

                            }
                        }


                        $info_elements[] = array(
                            'billon'=>$billon->getNumeroBillon(),
                            'lng'=>$element->getLng(),
                            'lrg'=>$element->getLrg(),
                            'ep'=>$element->getEp(),
                            'nb'=>$element->getNombre(),
                            'vol'=>$element->getVolume(),
                            'bille'=>$bille,
                            'numpagebrh'=>$numero_feuillet,
                            'usine_origine'=>$usine_origine,
                            'numero_doc'=>$numero_doc,
                            'type_doc'=>$type_doc,
                            'foret'=>$foret,
                            'region'=>$region,
                            'attributaire'=>$attributaire,
                            'x'=>$x,
                            'y'=>$y,
                        );
                    }
                }

                return  new JsonResponse(json_encode($info_elements));


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }
}
