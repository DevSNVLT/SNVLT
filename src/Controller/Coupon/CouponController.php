<?php

namespace App\Controller\Coupon;

use App\Controller\Services\AdministrationService;
use App\Controller\Services\Utils;
use App\Entity\Admin\Coupon;
use App\Entity\User;
use App\Form\Admin\Coupon\CouponType;
use App\Repository\Admin\CouponRepository;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CouponController extends AbstractController
{
    public function __construct(
        private AdministrationService $administrationService,
        private TranslatorInterface $translator,
        private  Utils $utils)
    {
    }

    #[Route('snvlt/coupon/all', name: 'app_coupon_coupon')]
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
                $this->isGranted('ROLE_MINEF') or
                $this->isGranted('ROLE_ADMIN')
            ) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();


        return $this->render('coupon/coupon/index.html.twig', [
            'liste_menus'=>$menus->findOnlyParent(),
            "all_menus"=>$menus->findAll(),
            'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
            'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
            'groupe'=>$code_groupe,
            'liste_parent'=>$permissions,
            'exercice'=>$this->administrationService->getAnnee()->getAnnee(),
            'mes_coupons'=>$registry->getRepository(Coupon::class)->findAll()
        ]);

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }

    #[Route('snvlt/coupon/add', name: 'coupon.edit')]
    public function add_coupon(
        Coupon $coupon = null,
        ManagerRegistry $doctrine,
        Request $request,
        CouponRepository $coupons,
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
            if ($this->isGranted('ROLE_INDUSTTRIEL'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();


                    $coupon = new Coupon();

                $date_jour = new \DateTime();

                $nb_coupons =  1;
                foreach ($doctrine->getRepository(Coupon::class)->findAll() as $coup){
                    $nb_coupons = $nb_coupons + 1;
                }

                $code_coupon = $this->utils->uniqidReal(5). $nb_coupons . $date_jour->format('Y');
                $form = $this->createForm(CouponType::class, $coupon);

                $form->handleRequest($request);


                if ( $form->isSubmitted() && $form->isValid() ){
                    $coupon->setCodeCoupon(strtoupper($code_coupon));
                    $coupon->setCreatedAt($date_jour);
                    $coupon->setCreatedBy($user);

                    $manager = $doctrine->getManager();
                    $manager->persist($coupon);
                    $manager->flush();

                    $this->addFlash('success',$this->translator->trans("Le coupon a été généré avec succès"));
                    return $this->redirectToRoute("app_coupon_coupon");
                } else {
                    return $this->render('references/coupon/add-coupon.html.twig',[
                        'form' =>$form->createView(),
                        'ref_coupons' => $coupons->findAll(),
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'groupe'=>$code_groupe,
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'coupons'=>$coupons->findAll(),
                        'liste_parent'=>$permissions
                    ]);
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }
    
}
