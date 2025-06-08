<?php

namespace App\Controller\Transformation;

use App\Entity\DocStats\Pages\Pagebrh;
use App\Entity\DocStats\Saisie\Lignepagelje;
use App\Entity\References\Essence;
use App\Entity\References\TypeTransformation;
use App\Entity\Transformation\Billon;
use App\Entity\Transformation\Contrat;
use App\Entity\Transformation\Elements;
use App\Entity\Transformation\FicheLot;
use App\Entity\Transformation\FicheLotProd;
use App\Entity\User;
use App\Repository\Administration\NotificationRepository;
use App\Repository\DocStats\Pages\PagebrhRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\Transformation\BillonRepository;
use App\Repository\Transformation\FicheJourTransfoRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Element;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FicheJourTransfoController extends AbstractController
{
    #[Route('/snvlt/transformation/fiche/jour/transfo', name: 'app_fiche_jour_transfo')]
    public function index(ManagerRegistry $registry,
                          BillonRepository $billonRepository,
                          FicheJourTransfoRepository $fichesRepository,
                          Request $request,
                          MenuPermissionRepository $permissions,
                          MenuRepository $menus,
                          GroupeRepository $groupeRepository,
                          UserRepository $userRepository,
                          User $user = null,
                          NotificationRepository $notification): Response
    {

        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $date_du_jour = new \DateTimeImmutable();

                return $this->render('transformation/fiche_jour_transfo/index.html.twig', [
                    'essences' => $registry->getRepository(Essence::class)->findBy([],['nom_vernaculaire'=>'ASC']),
                    'billes_non_decoupes'=>$registry->getRepository(Lignepagelje::class)->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'liste_parent'=>$permissions,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'groupe'=>$code_groupe
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }

     #[Route('/snvlt/elts/add_data/{lng}/{lrg}/{ep}/{nb}/{code_billon}/{date_enr}/{id_essence}/{id_transfo}', name: 'elts_billons')]
    public function elts_billons(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        int $lng,
        int $lrg,
        int $ep,
        int $nb,
        int $code_billon,
        string $date_enr,
        int $id_essence,
        int $id_transfo,
        User $user = null,
        NotificationRepository $notification,
        PagebrhRepository $pages_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL'))
            {

                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $essence = $registry->getRepository(Essence::class)->find($id_essence);
                $transfo = $registry->getRepository(TypeTransformation::class)->find($id_transfo);


                if(strval(intval($lng)) && strval(intval($lrg)) && strval(intval($ep)) && strval(intval($nb)) && strval(intval($code_billon)) && $date_enr){
                        $billon = $registry->getRepository(Billon::class)->find($code_billon);
                        if ($billon){
                            $fiche_lot_prod = $registry->getRepository(FicheLotProd::class)->findOneBy(['code_usine'=>$user->getCodeindustriel(), 'date_fiche'=>\DateTime::createFromFormat('Y-m-d', $date_enr)]);
                            if (!$fiche_lot_prod){
                                $fiche_lot_prod = new FicheLotProd();
                                $fiche_lot_prod->setDateFiche(\DateTime::createFromFormat('Y-m-d', $date_enr));
                                $fiche_lot_prod->setCodeUsine($user->getCodeindustriel());
                                $registry->getManager()->persist($fiche_lot_prod);
                                $fiche_lot_prod->setNumero($fiche_lot_prod->getId()."-".$fiche_lot_prod->getCodeUsine()->getId());

                            }

                            $element = $registry->getRepository(Elements::class)->findOneBy([
                                'lng'=>$lng,
                                'lrg'=>$lrg,
                                'ep'=>$ep,
                                'code_fiche_prod'=>$fiche_lot_prod
                            ]);

                            $volume = (($lng/100) * ($lrg/100) * ($ep/100) * $nb);

                            if ($element){
                                $element->setNombre($element->getNombre() + $nb);
                                $element->setVolume($element->getVolume() + $volume);
                            } else {
                                $element = new Elements();
                                $element->setLng($lng);
                                $element->setLrg($lrg);
                                $element->setEp($ep);
                                $element->setNombre($nb);
                                $element->setDateEnr(\DateTime::createFromFormat('Y-m-d', $date_enr));
                                $element->setCreatedAt(new \DateTime());
                                $element->setCreatedBy($user);
                                $element->setCodeBillon($billon);
                                $element->setCodeTypeTransfo($transfo);
                                $element->setCodeEssence($essence);
                                $element->setCodeFicheProd($fiche_lot_prod);
                                $element->setVolume($volume);
                            }

                            $registry->getManager()->persist($element);

                            //Mise à jour du volume de la fiche Lot de Production
                            $fiche_lot_prod->setVolume($fiche_lot_prod->getVolume() + $element->getVolume());


                            $registry->getManager()->flush();

                            return  new JsonResponse(json_encode("SUCCESS"));
                        }else {
                            return  new JsonResponse(json_encode("FAILED"));
                        }

                     }else {
                            return  new JsonResponse(json_encode("FAILED"));
                        }

                } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }

    #[Route('/snvlt/elements/add_data/deroulage/{lng}/{lrg}/{ep}/{nb}/{id_fiche}/{date_enr}/{id_essence}/{id_transfo}', name: 'add_deroulage-json')]
    public function add_deroulage(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        int $lng,
        int $lrg,
        int $ep,
        float $nb,
        int $id_fiche,
        string $date_enr,
        int $id_essence,
        int $id_transfo,
        User $user = null,
        NotificationRepository $notification,
        PagebrhRepository $pages_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL'))
            {

                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $essence = $registry->getRepository(Essence::class)->find($id_essence);
                $transfo = $registry->getRepository(TypeTransformation::class)->find($id_transfo);

                if(strval(intval($lng)) && strval(intval($lrg)) && strval(intval($ep)) && strval(intval($nb)) && strval(intval($id_fiche)) && $date_enr){
                    $fiche_lot = $registry->getRepository(FicheLot::class)->find($id_fiche);
                    if ($fiche_lot && $essence && $transfo){

                        $fiche_lot_prod = $registry->getRepository(FicheLotProd::class)->findOneBy(['code_usine'=>$user->getCodeindustriel(), 'date_fiche'=>\DateTime::createFromFormat('Y-m-d', $date_enr)]);
                        if (!$fiche_lot_prod){
                            $fiche_lot_prod = new FicheLotProd();
                            $fiche_lot_prod->setDateFiche(\DateTime::createFromFormat('Y-m-d', $date_enr));
                            $fiche_lot_prod->setCodeUsine($user->getCodeindustriel());
                            $fiche_lot_prod->setCodeFicheLot($fiche_lot);
                            $registry->getManager()->persist($fiche_lot_prod);
                            $fiche_lot_prod->setNumero($fiche_lot_prod->getId()."-".$fiche_lot_prod->getCodeUsine()->getId());
                            //$registry->getManager()->flush();
                        }

                        $element = $registry->getRepository(Elements::class)->findOneBy([
                            'lng'=>$lng,
                            'lrg'=>$lrg,
                            'ep'=>$ep,
                            'code_fiche_prod'=>$fiche_lot_prod
                        ]);


                        $nb_elements  = $nb;
                        $volume = (($lng/100) * ($lrg/100) * ($ep/1000) * $nb);
                        //dd(round($nb_elements, 0));
                        if ($element){
                            $element->setNombre($element->getNombre() + $nb_elements);
                            $element->setVolume($element->getVolume() + $volume);
                        } else {
                            $element = new Elements();
                            $element->setNombre($nb_elements);
                            $element->setCreatedAt(new \DateTime());
                            $element->setLng($lng);
                            $element->setLrg($lrg);
                            $element->setEp($ep);
                            $element->setDateEnr(\DateTime::createFromFormat('Y-m-d', $date_enr));
                            $element->setCreatedBy($user);
                            $element->setCodeFicheTronconnage($fiche_lot);
                            $element->setCodeFicheProd($fiche_lot_prod);
                            $element->setCodeTypeTransfo($transfo);
                            $element->setCodeEssence($essence);
                            $element->setVolume($volume);
                        }

                        $registry->getManager()->persist($element);

                        //Mise à jour du volume de la fiche Lot de Production
                        $fiche_lot_prod->setCodeFicheLot($fiche_lot);
                        $fiche_lot_prod->setVolume($fiche_lot_prod->getVolume() + $element->getVolume());

                        $registry->getManager()->flush();

                        return  new JsonResponse(json_encode("SUCCESS"));
                    }else {
                        return  new JsonResponse(json_encode("FAILED"));
                    }

                }else {
                    return  new JsonResponse(json_encode("FAILED"));
                }

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }
    #[Route('/snvlt/billon/liste_elts/{id_billon}', name: 'liste_elts_billon')]
    public function liste_elts_billon(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        int $id_billon,
        User $user = null,
        NotificationRepository $notification,
        PagebrhRepository $pages_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL'))
            {

                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                 $billon = $registry->getRepository(Billon::class)->find($id_billon);
                $my_elts = array();
                    if ($billon){


                        $elts = $registry->getRepository(Elements::class)->findBy(['code_billon'=>$billon], ['date_enr'=>'DESC']);

                       foreach($elts as $elt) {
                           $my_elts[] = array(
                               'id_elts'=>$elt->getId(),
                               'lng_elts'=>$elt->getLng(),
                               'lrg_elts'=>$elt->getLrg(),
                               'ep_elts'=>$elt->getEp(),
                               'nb_elts'=>$elt->getNombre(),
                               'vol_elts'=>$elt->getVolume()

                           );

                       }


                        }
                return  new JsonResponse(json_encode($my_elts));


                } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }

    #[Route('/snvlt/billon/liste_elts/deroulage/{id_fiche}', name: 'liste_elts_billon_deroulage')]
    public function liste_elts_billon_deroulage(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        int $id_fiche,
        User $user = null,
        NotificationRepository $notification,
        PagebrhRepository $pages_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL'))
            {

                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $fiche_lot= $registry->getRepository(FicheLot::class)->find($id_fiche);
                $my_elts = array();
                if ($fiche_lot){


                    $elts = $registry->getRepository(Elements::class)->findBy(['code_fiche_tronconnage'=>$fiche_lot], ['date_enr'=>'DESC']);

                    foreach($elts as $elt) {

                        $my_elts[] = array(
                            'id_elts'=>$elt->getId(),
                            'lng_elts'=>$elt->getLng(),
                            'lrg_elts'=>$elt->getLrg(),
                            'ep_elts'=>$elt->getEp(),
                            'nb_elts'=>$elt->getNombre(),
                            'vol_elts'=>round($elt->getVolume(),3)

                        );

                    }


                }
                return  new JsonResponse(json_encode($my_elts));


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }


    #[Route('/snvlt/elt/edit/{id_elt}/{lng}/{lrg}/{ep}/{nb}', name: 'edit_elt')]
    public function edit_elt(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        int $id_elt,
        int $lng,
        int $lrg,
        int $ep,
        int $nb,
        User $user = null,
        NotificationRepository $notification,
        PagebrhRepository $pages_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL'))
            {

                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $element= $registry->getRepository(Elements::class)->find($id_elt);

                if ($element){
                        $element->setLng($lng);
                        $element->setLrg($lrg);
                        $element->setEp($ep);
                        $element->setNombre($nb);

                        if($element->getCodeBillon()->getTypeTransformation()->getId() == 1){ /*SCIAGE : cm*/
                            $volume = (($lng/100) * ($lrg/100) * ($ep/100) * $nb);
                        } elseif($element->getCodeBillon()->getTypeTransformation()->getId() == 2) { /*DEROULAGE : mm*/
                            $volume = (($lng/100) * ($lrg/100) * ($ep/1000) * $nb);
                        } elseif ($element->getCodeBillon()->getTypeTransformation()->getId() == 3){ /*TRANCHAGE : mm*/
                            $volume = (($lng/100) * ($lrg/100) * ($ep/1000) * $nb);
                        }


                        $element->setVolume($volume);

                        $registry->getManager()->persist($element);
                        $registry->getManager()->flush();

                        return  new JsonResponse(json_encode("SUCCESS"));
                    } else {

                    return  new JsonResponse(json_encode("FAILED"));
                }



            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }

    #[Route('/snvlt/elt/search/{id_elt}', name: 'search_elt')]
    public function search_elt(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        int $id_elt,
        User $user = null,
        NotificationRepository $notification,
        PagebrhRepository $pages_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL'))
            {

                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $element= $registry->getRepository(Elements::class)->find($id_elt);
                $elt= array();
                if ($element){
                    $elt[] = array(
                        'lng_elt'=>$element->getLng(),
                        'lrg_elt'=>$element->getLrg(),
                        'ep_elt'=>$element->getEp(),
                        'nb_elt'=>$element->getNombre()
                    );

                    return  new JsonResponse(json_encode($elt));
                } else {

                    return  new JsonResponse(json_encode(false));
                }



            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }


    #[Route('/snvlt/elements/del/{id_elt}', name: 'delete_elt')]
    public function delete_elt(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        int $id_elt,
        User $user = null,
        NotificationRepository $notification,
        PagebrhRepository $pages_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL'))
            {

                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $element= $registry->getRepository(Elements::class)->find($id_elt);
                $elt= array();
                if ($element){
                    if ($user->isIsResponsable()){
                        $registry->getManager()->remove($element);
                        $registry->getManager()->flush();

                        $elt[] = array(
                            'code'=>'SUCCESS'
                        );
                    } else {
                        $elt[] = array(
                            'code'=>'NO_RESPONSABLE'
                        );
                    }
                } else {
                    $elt[] = array(
                        'code'=>'NO_FOUND'
                    );
                }

                return  new JsonResponse(json_encode($elt));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }
    #[Route('snvlt/elt/search/edit/data/{donnees}', name: 'edit_elements_elt')]
    public function edit_elements_elt(
        Request $request,
        UserRepository $userRepository,
        string $donnees,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL'))
            {
                $elt_array= array();
                if($donnees){
                    $arraydata = json_decode($donnees);

                    $elt = $registry->getRepository(Elements::class)->find((int) $arraydata->id_elt);
                    if ($elt){
                        $elt->setEp((int) $arraydata->ep);
                        $elt->setLrg((int) $arraydata->lrg);
                        $elt->setLng((int) $arraydata->lng);
                        $elt->setNombre((int) $arraydata->nb);

                        $volume =  ((((int) $arraydata->lng)/100) * (((int) $arraydata->lrg)/100) * (((int) $arraydata->ep)/1000) * ((int) $arraydata->nb));
                        $elt->setVolume($volume);

                        $registry->getManager()->persist($elt);
                        $registry->getManager()->flush();
                        $elt_array[] = array(
                            'code'=>'SUCCESS'
                        );
                    } else {
                        $elt_array[] = array(
                            'code'=>'NOT_FOUND'
                        );
                    }
                } else {
                    $elt_array[] = array(
                        'code'=>'DATA_ERROR'
                    );
                }
                return  new JsonResponse(json_encode($elt_array));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }
}
