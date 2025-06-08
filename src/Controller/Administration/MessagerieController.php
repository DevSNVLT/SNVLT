<?php

namespace App\Controller\Administration;

use App\Entity\Administration\DocStatsGen;
use App\Entity\User;
use App\Repository\Administration\MessagerieRepository;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\PageDocGenRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class MessagerieController extends AbstractController

{
    private $translator;
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    #[Route('/snvlt/messagerie', name: 'app_messagerie')]
    public function index(ManagerRegistry $registry,
                          Request $request,
                          MenuPermissionRepository $permissions,
                          MenuRepository $menus,
                          GroupeRepository $groupeRepository,
                          UserRepository $userRepository,
                          User $user = null,
                          NotificationRepository $notification,
                          MessagerieRepository $messagerieRepository): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {

                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

            return $this->render('administration/messagerie/index.html.twig', [
                //'qr_code_doc' => $response,
                'liste_menus'=>$menus->findOnlyParent(),
                "all_menus"=>$menus->findAll(),
                'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                'liste_parent'=>$permissions,
                'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                'messagerie'=>$messagerieRepository->findBy(['code_utilisateur'=>$user])
            ]);
        }

    }

}
