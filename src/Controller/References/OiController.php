<?php

namespace App\Controller\References;


use App\Controller\Services\AdministrationService;
use App\Entity\References\Oi;
use App\Entity\User;
use App\Form\References\OiType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\DemandeOperateurRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\OiRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class OiController extends AbstractController
{

    public function __construct(private TranslatorInterface $translator, private AdministrationService $administrationService)
    {

    }

    #[Route('snvlt/ref/oi/list', name: 'ref_ois')]
    public function listing(OiRepository $ois,
    MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        ManagerRegistry $doctrine,
        Request $request,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification): Response
    {
        //dd($request);
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                return $this->render('references/oi/index.html.twig',
                    [

                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'groupe'=>$code_groupe,
                        'ois'=>$ois->findAll(),
                        'liste_parent'=>$permissions
                    ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    #[Route('/edit/oi/{id_oi?0}', name: 'oi.edit')]
    public function editOi(
        ManagerRegistry $doctrine,
        Request $request,
        OiRepository $ois,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        int $id_oi,
        UserRepository $userRepository,
        NotificationRepository $notification): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();



        $titre = $this->translator->trans("Modifier un Observateur Indépendant");
        $oi = $ois->find($id_oi);
                $new = false;
                $action = "MODIFICATION";
                if(!$oi){
                    $new = true;
                    $action = "AJOUT";
                    $oi = new Oi();
                    $titre = $this->translator->trans("Ajouter un Observateur Indépendant");
                    $oi->setCreatedAt(new \DateTimeImmutable());
                    $oi->setCreatedBy($user);
                }

            $form = $this->createForm(OiType::class, $oi);

            $form->handleRequest($request);

            if ( $form->isSubmitted() && $form->isValid() ){


                if(!$new){
                    $oi->setUpdatedAt(new \DateTimeImmutable());
                    $oi->setUpdatedBy($user);
                }


                $oi->setCode(strtoupper($form->get('code')->getData()));
                $oi->setSigle(strtoupper($form->get('sigle')->getData()));
                $oi->setRaisonSociale(strtoupper($form->get('raisonSociale')->getData()));
                $oi->setAdresse(strtoupper($form->get('adresse')->getData()));
                $oi->setPersonneRessource(strtoupper($form->get('personneRessource')->getData()));

                $manager = $doctrine->getManager();
                $manager->persist($oi);
                $manager->flush();


                if ($new){
                    $this->addFlash('success', "L'OI' " . $oi->getRaisonSociale() . " vient d'être créé.");
                    $decription = "L'OI' " . $oi->getRaisonSociale() . " a été ajouté par ". $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur();
                } else {
                    $this->addFlash('success', "L'OI' " . $oi->getRaisonSociale() . " a été mise à jour");
                    $decription = "L'OI' " . $oi->getRaisonSociale() . " a été modifié par ". $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur();
                }

                // Ajout dans le log SNVLT
                $this->administrationService->save_action(
                    $user,
                    "Observateur Indépendant",
                    $action,
                    new \DateTimeImmutable(),
                    $decription
                );

                return $this->redirectToRoute("ref_ois");
            } else {
                return $this->render('references/oi/add-oi.html.twig',[
                    'form' =>$form->createView(),
                    'titre'=>$titre,
                    'ref_ois' => $ois->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                    'ois'=>$ois->findAll(),
                    'liste_parent'=>$permissions
                ]);
            }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
            }

    }
    #[Route('snvlt/ois/list', name: 'liste_ois')]
    public function oi_json(Request $request, OiRepository $oiRepository):Response{
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
                {

                    $liste_ois = $oiRepository->findWithResponsable();

                    $response = array();
                    foreach ($liste_ois as $oi) {
                        $response[] = array(
                            'id' => $oi->getId(),
                            'denomination' => $oi->getSigle()
                        );
                    }

                    return new JsonResponse(json_encode($response));

                } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
    #[Route('snvlt/ref/oi/data_json', name: 'json_ois')]
    public function json_ois(OiRepository $repository,
                                      UserRepository $userRepository,
                                      Request $request
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

                $liste_ois= $repository->findBy([], ['sigle'=>'ASC']);
                foreach ( $liste_ois as $oi){

                    $response[] =  array(
                        'id'=>$oi->getId(),
                        'sigle'=>$oi->getSigle(),
                        'rs'=>$oi->getSigle()
                    );

                }


                return  new  JsonResponse(json_encode($response));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
}
