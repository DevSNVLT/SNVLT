<?php

namespace App\Controller\Administration;

use App\Controller\Services\AdministrationService;
use App\Entity\Autorisation\Attribution;
use App\Entity\References\Foret;
use App\Entity\References\TypeForet;
use App\Entity\User;
use App\Repository\Administration\NotificationRepository;
use App\Repository\Autorisations\AttributionRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DernierNumeroArbreController extends AbstractController
{
    public function __construct(
        private AdministrationService $administrationService
    )
    {
    }

    #[Route('/administration/dernierNumeroArbre', name: 'app_dernier_numero_arbre')]
    public function index(ManagerRegistry $registry,
                          MenuRepository $menus,
                          MenuPermissionRepository $permissions,
                          Request $request,
                          UserRepository $userRepository,
                          NotificationRepository $notification
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN') or  $this->isGranted('ROLE_EXPLOITANT'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                $liste_forets = array();
                if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN')){
                    foreach ($registry->getRepository(Foret::class)->findAll() as $foret){
                        $liste_forets[] = array(
                            'denomination'=>$foret->getDenomination(),
                            'id'=>$foret->getId(),
                            'type_foret'=>$foret->getCodeTypeForet()->getLibelle(),
                            'dernier_numero'=>$foret->getDernierNumero()
                        );
                    }

                } elseif ($this->isGranted('ROLE_EXPLOITANT')){
                    $attributions = $registry->getRepository(Attribution::class)->findBy(
                        [
                            'code_exploitant'=>$user->getCodeexploitant(),
                            'exercice'=>$this->administrationService->getAnnee(),
                            'statut'=>true
                        ]);
                    foreach ($attributions as $attribution){
                        $liste_forets[] = array(
                            'denomination'=>$attribution->getCodeForet()->getDenomination(),
                            'id'=>$attribution->getCodeForet()->getId(),
                            'type_foret'=>$attribution->getCodeForet()->getCodeTypeForet()->getLibelle(),
                            'dernier_numero'=>$attribution->getCodeForet()->getDernierNumero()
                        );
                    }
                }

                sort($liste_forets);
        return $this->render('administration/dernier_numero_arbre/index.html.twig', [
            'liste_menus'=>$menus->findOnlyParent(),
            "all_menus"=>$menus->findAll(),
            'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
            'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
            'groupe'=>$code_groupe,
            'liste_parent'=>$permissions,
            'forets'=>$liste_forets,
            'type_forets'=>$registry->getRepository(TypeForet::class)->findAll()
        ]);
            } else {
                return  $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
}
