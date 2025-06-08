<?php

namespace App\Controller\Transformation;

use App\Entity\DocStats\Entetes\Documentlje;
use App\Entity\DocStats\Pages\Pagelje;
use App\Entity\DocStats\Saisie\Lignepagelje;
use App\Entity\References\Essence;
use App\Entity\References\TypeTransformation;
use App\Entity\Transformation\Billon;
use App\Entity\Transformation\Details2Transfo;
use App\Entity\Transformation\Elements;
use App\Entity\Transformation\Fiche2Transfo;
use App\Entity\Transformation\Pdt2Transfo;
use App\Entity\User;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\Transformation\BillonRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecondTransfoController extends AbstractController
{
    #[Route('/transformation/second/transfo', name: 'app_transformation_second_transfo')]
    public function index(ManagerRegistry $registry,
                          BillonRepository $billonRepository,
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

                return $this->render('transformation/second_transfo/index.html.twig', [
                    //'qr_code_doc' => $response,
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

    #[Route('/snvlt/fiche2transfo*/add/{id_produit}/{date_transfo}/{qte}', name: 'add_fiche2transfo')]
    public function add_fiche2transfo(
        ManagerRegistry $registry,
        Request $request,
        int $id_produit,
        string $date_transfo,
        int $qte,
        UserRepository $userRepository): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $liste_fiches = array();

                $produit  =  $registry->getRepository(Pdt2Transfo::class)->find($id_produit);
                $date_transformation = \DateTime::createFromFormat('Y-m-d', $date_transfo);
                if ($produit && (int) $qte && $date_transformation){
                    $fiche = new Fiche2Transfo();

                    $fiche->setProduit($produit);
                    $fiche->setDateTransformation($date_transformation);
                    $fiche->setQte($qte);
                    $fiche->setCodeindustriel($user->getCodeindustriel());

                    $registry->getManager()->persist($fiche);
                    $registry->getManager()->flush();

                    $liste_fiches[] = array(
                        'code'=>'SUCCESS'
                    );
                } else  {
                    $liste_fiches[] = array(
                        'code'=>'BAD_REQUEST'
                    );
                }


                return new JsonResponse(json_encode($liste_fiches));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/fiche2transfo*/liste/pdts2tr', name: 'app_transformation_2_transfo_listing')]
    public function app_transformation_2_transfo_listing(
        ManagerRegistry $registry,
        Request $request,
        UserRepository $userRepository): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $liste_fiches = array();

                $produits  =  $registry->getRepository(Pdt2Transfo::class)->findAll();

                foreach ($produits as $produit){
                    $liste_fiches[] = array(
                        'id_pdt'=>$produit->getId(),
                        'produit'=>$produit->getNomPdt()
                    );

                }


                return new JsonResponse(json_encode($liste_fiches));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/contrats/fiche2tr/list/{id_produit}', name: 'app_liste_fiche2tr')]
    public function app_liste_fiche2tr(
        ManagerRegistry $registry,
        Request $request,
        int $id_produit,
        UserRepository $userRepository): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $liste_fiches = array();
                $produit  =  $registry->getRepository(Pdt2Transfo::class)->find($id_produit);
                $fiches  =  $registry->getRepository(Fiche2Transfo::class)->findBy(['produit'=>$produit, 'codeindustriel'=>$user->getCodeindustriel()], ['id'=>'DESC']);


                foreach ($fiches as $fiche){
                    $liste_elts = $registry->getRepository(Details2Transfo::class)->findBy(['code_fiche2'=>$fiche]);
                    $nb_elts = 0;
                    $vol_elts = 0;
                    foreach ($liste_elts as $elt){
                        $nb_elts = $nb_elts + $elt->getNb();
                        $vol_elts = $vol_elts + $elt->getVolume();
                    }

                    $liste_fiches[] = array(
                        'id_pdt'=>$fiche->getId(),
                        'produit'=>$fiche->getProduit()->getNomPdt(),
                        'transfo'=>$fiche->getProduit()->getTypeProduit()->getLibelle(),
                        'nb_elts'=>$nb_elts,
                        'vol_elts'=>$vol_elts,
                        'date_fiche'=>$fiche->getDateTransformation()->format('d/m/Y')
                    );

                }


                return new JsonResponse(json_encode($liste_fiches));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/contrats/fiche2tr/recherche/{id_fiche}/{lng}/{lrg}/{ep}/{id_essence}', name: 'app_recherche_fiche2tr_elt')]
    public function app_recherche_fiche2tr_elt(
        ManagerRegistry $registry,
        Request $request,
        int $id_fiche,
        int $lng,
        int $lrg,
        int $ep,
        int $id_essence,
        UserRepository $userRepository): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

               // $liste_fiches = array();
                $fiche  =  $registry->getRepository(Fiche2Transfo::class)->find($id_fiche);
                $essence = $registry->getRepository(Essence::class)->find($id_essence);

                $nb_eltss = array();
                $nb_elements = 0;
                $nb_restant = 0;
                $doc_ljes = $registry->getRepository(Documentlje::class)->findBy(['code_usine'=>$user->getCodeindustriel()]);
                foreach ($doc_ljes as $lje){
                    $pages_lje = $registry->getRepository(Pagelje::class)->findBy(['code_doclje'=>$lje]);
                    foreach ($pages_lje as $page){
                        $lignes_lje = $registry->getRepository(Lignepagelje::class)->findBy(['code_pagelje'=>$page, 'essence'=>$essence]);
                        foreach ($lignes_lje as $ligne){
                            $billons = $registry->getRepository(Billon::class)->findBy(['code_lignepagelje'=>$ligne, 'type_transformation'=>$fiche->getProduit()->getTypeProduit()]);
                           //dd($fiche->getProduit()->getTypeProduit());
                            foreach ($billons as $billon){
                                $elements = $registry->getRepository(Elements::class)->findBy([
                                    'code_billon'=>$billon,
                                    'lng'=>$lng,
                                    'lrg'=>$lrg,
                                    'ep'=>$ep
                                ]);


                                foreach ($elements as $element){
                                    $nb_elements =$nb_elements + $element->getNombre();
                                }



                            }
                        }
                    }
                }
                $nb_eltss[] = array(
                    'nb'=>$nb_elements
                );



                return new JsonResponse(json_encode($nb_eltss));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }


    #[Route('/snvlt/fiche2transfo*/add_elts/{id_fiche}/{lng}/{lrg}/{ep}/{id_essence}/{qte}', name: 'add_elt_details_tr2')]
    public function add_elt_details_tr2(
        ManagerRegistry $registry,
        Request $request,
        int $id_fiche,
        int $lng,
        int $lrg,
        int $ep,
        int $id_essence,
        int $qte,
        UserRepository $userRepository): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $liste_fiches = array();

                $fiche  =  $registry->getRepository(Fiche2Transfo::class)->find($id_fiche);
                $essence  =  $registry->getRepository(Essence::class)->find($id_essence);
                if ($fiche){
                    $details = new Details2Transfo();

                    $details->setLng($lng);
                    $details->setLrg($lng);
                    $details->setEp($lng);
                    $details->setNb($qte);
                    $details->setCodeEssence($essence);
                    $details->setCodeFiche2($fiche);
                    $details->setVolume( $lng * $lrg * $ep * $qte / 1000000);

                    $registry->getManager()->persist($details);
                    $registry->getManager()->flush();

                    $liste_fiches[] = array(
                        'code'=>'SUCCESS'
                    );
                } else  {
                    $liste_fiches[] = array(
                        'code'=>'BAD_REQUEST'
                    );
                }


                return new JsonResponse(json_encode($liste_fiches));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
}
