<?php

namespace App\Controller\References;


use App\Entity\References\Dr;
use App\Entity\User;
use App\Form\References\DrType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\CantonnementRepository;
use App\Repository\References\DrRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DrController extends AbstractController
{

    public function __construct(private TranslatorInterface $translator)
    {

    }

    #[Route('/dr', name: 'ref_dr')]
    public function index(DrRepository $drs): Response
    {
        return $this->render('dr/liste.html.twig', [
            'ref_drs' => $drs->findAll(),
        ]);
    }

    #[Route('snvlt/ref/diregionale/{id_dr?0}', name: 'ref_drs')]
    public function listing(DrRepository $drs,
    MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        Dr $dr = null,
        ManagerRegistry $doctrine,
        int $id_dr,
        Request $request,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification
        ): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {
            $user = $userRepository->find($this->getUser());
            $code_groupe = $user->getCodeGroupe()->getId();

            $titre = $this->translator->trans("Edit Regional Direction");
            $dr = $drs->find($id_dr);
            //dd($dr);
            if(!$dr){
                $new = true;
                $dr = new Dr();
            }
            $form = $this->createForm(DrType::class, $dr);

            $form->handleRequest($request);

            if ( $form->isSubmitted() && $form->isValid() ){


                $manager = $doctrine->getManager();
                $manager->persist($dr);
                $manager->flush();

                $this->addFlash('success',$this->translator->trans("La Direction Régionale vient dêtre éditée avec succès"));
                return $this->redirectToRoute("ref_dr");
            } else {
                return $this->render('references/dr/index.html.twig', [
                    'ref_drs' => $drs->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'liste_drs' => $drs->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'form' =>$form->createView(),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'titre'=>$titre,
                    'liste_parent'=>$permissions
                ]);
            }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
            }
    }


    #[Route('/edit/dr/{id_dr?0}', name: 'dr.edit')]
    public function editDr(
        Dr $dr = null,
        ManagerRegistry $doctrine,
        Request $request,
        DrRepository $drs,
        CantonnementRepository $cantonnementRepository,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        GroupeRepository $groupeRepository,
        int $id_dr,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $titre = $this->translator->trans("Edit Regional Direction");
                $dr = $drs->find($id_dr);
                //dd($dr);
                if(!$dr){
                    $new = true;
                    $dr = new Dr();
                    $titre = $this->translator->trans("Add Regional Direction");
                }

        /*        $session = $request->getSession();
                if (!$session->has("user_session")){
                    $this->addFlash('error', 'Vous devez vous reconnecter pour avoir accès à SNVLT');
                    return $this->redirectToRoute('app_login');
                } else {*/

                    $new = false;
                    if(!$dr){
                        $new = true;
                        $dr = new Dr();
                    }
                    $form = $this->createForm(DrType::class, $dr);

                    $form->handleRequest($request);

                    if ( $form->isSubmitted() && $form->isValid() ){


                        $manager = $doctrine->getManager();
                        $manager->persist($dr);
                        $manager->flush();

                        $this->addFlash('success', $this->translator->trans("La Direction Régionale vient dêtre éditée avec succès"));
                        return $this->redirectToRoute("ref_drs");
                    } else {
                        return $this->render('references/dr/add.html.twig',[
                            'form' =>$form->createView(),
                            'titre'=>$titre,
                            'ref_drs' => $drs->findAll(),
                            'liste_menus'=>$menus->findOnlyParent(),
                            "all_menus"=>$menus->findAll(),
                            'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                            'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                            'groupe'=>$code_groupe,
                            'liste_parent'=>$permissions,
                            'liste_cantonnement'=>$cantonnementRepository->findBy(['code_dr'=>$dr])
                        ]);
                    }
                    } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }


    #[Route('/snvlt/ref/edit-dr/{id_dr?0}', name: 'edit-dr')]
    public function affiche(DrRepository $doctrine, $id_dr, Dr $dr = null): Response
    {

        $dr = $doctrine->find($id_dr);
        //dd($dr);
        if(!$dr) {
            throw $this->createNotFoundException($this->translator->trans('The requested entity was not found.'));
        }

        $DrArray = json_encode([
            'id' => $dr->getId(),
            'code_dr' => $dr->getNumeroDr(),
            'nom_vernaculaire'=>$dr->getNomVernaculaire(),
            'nom_scientifique' => $dr->getNomScientifique(),
            'categorie' => $dr->getCategorieDr(),
            'dm_minima' => $dr->getDmMinima(),
            'famille_dr' => $dr->getFamilleDr(),
            'taxe_abattage'=>$dr->getTaxeAbattage(),
            'taxe_preservation'=>$dr->getTaxePreservation()
        ]);
        return new Response($DrArray);
    }
    #[Route('snvlt/ref/dr/data_json', name: 'json_dr')]
    public function json_Dr(DrRepository $Drs,
                            MenuRepository $menus,
                            MenuPermissionRepository $permissions,
                            GroupeRepository $groupeRepository,
                            UserRepository $userRepository,
                            ManagerRegistry $doctrine,
                            Request $request,
                            NotificationRepository $notificationRepository,
    ): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $response = array();

                //------------------------- Filtre les CP par type Opérateur ------------------------------------- //

                $liste_Drs= $Drs->findBy([], ['denomination'=>'ASC']);
                foreach ( $liste_Drs as $Dr){

                    $response[] =  array(
                        'id'=>$Dr->getId(),
                        'sigle'=>$Dr->getDenomination(),
                        'rs'=>$Dr->getDenomination(),
                    );

                }


                return  new  JsonResponse(json_encode($response));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

}
