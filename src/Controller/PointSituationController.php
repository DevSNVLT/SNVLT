<?php

namespace App\Controller;

use App\Controller\Services\AdministrationService;
use App\Entity\DocStats\DocsStats;
use App\Entity\References\TypeDocumentStatistique;
use App\Entity\User;
use App\Repository\Administration\NotificationRepository;
use App\Repository\DocStats\Entetes\DocumentbrhRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PointSituationController extends AbstractController
{
    public function __construct(private AdministrationService $administrationService)
    {
    }

    #[Route('/point/situation', name: 'app_point_situation')]
    public function index(
        Request $request,
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
            if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE') or $this->isGranted('ROLE_ADMINISTRATIF')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                return $this->render('point_situation/index.html.twig', [
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'groupe'=>$code_groupe,
                    'liste_parent'=>$permissions,
                    'exercice'=>$this->administrationService->getAnnee()->getAnnee(),
                    'documents'=>$registry->getRepository(TypeDocumentStatistique::class)->findAll(),
                    'docstats'=>$registry->getRepository(DocsStats::class)->findBy([],['nb_delivres'=>'DESC'], 5, 0),
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
}
