<?php

namespace App\Controller\Transformation;

use App\Controller\Services\AdministrationService;
use App\Entity\Admin\Coupon;
use App\Entity\Admin\DocumentsCoupon;
use App\Entity\References\DocumentOperateur;
use App\Entity\References\TypeOperateur;
use App\Entity\References\Usine;
use App\Entity\Transformation\Contrat;
use App\Entity\User;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\Transformation\ContratRepository;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\Persistence\ManagerRegistry;
use Monolog\DateTimeImmutable;
use phpDocumentor\Reflection\Utils;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CouponAppController extends AbstractController
{
    public function __construct(private \App\Controller\Services\Utils $utils,
                                private AdministrationService $administrationService,
                                private EmailVerifier $emailVerifier,
                                private MailerInterface $mailer,
                                private TranslatorInterface $translator,
                               )
    {
    }

    #[Route('/coupon/app', name: 'app_coupons')]
    public function index(ManagerRegistry $registry,
                          ContratRepository $contratRepository,
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
                if ($this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF'))
                {
                    $user = $userRepository->find($this->getUser());
                    $code_groupe = $user->getCodeGroupe()->getId();

                    $role_utilisateur = "";
                    if ($this->isGranted('ROLE_INDUSTRIEL')){
                        $role_utilisateur = "IND";
                    }elseif ($this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')){
                        $role_utilisateur = "MINEF";
                    }

            return $this->render('transformation/coupon_app/index.html.twig', [
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'contrats'=>$contratRepository->findBy(['code_usine'=>$user->getCodeindustriel()]),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'liste_parent'=>$permissions,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'groupe'=>$code_groupe,
                    'role_utilisateur'=>$role_utilisateur,
                    'industriels'=>$registry->getRepository(Usine::class)->findBy([],['raison_sociale_usine'=>'ASC'])
                ]);
                } else {
                    return $this->redirectToRoute('app_no_permission_user_active');
                }
            }
    }

    #[Route('snvlt/coupon/add-new', name: 'add_coupon-new')]
    public function add_coupon_new(ManagerRegistry $registry,
                          ContratRepository $contratRepository,
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


                return $this->render('transformation/coupon_app/add.html.twig', [
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'contrats'=>$contratRepository->findBy(['code_usine'=>$user->getCodeindustriel()]),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'liste_parent'=>$permissions,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'groupe'=>$code_groupe,
                    'industriels'=>$registry->getRepository(Contrat::class)->findBy(['code_usine'=>$user->getCodeindustriel()],['created_at'=>'DESC']),
                    'docs_op'=>$user->getCodeindustriel()->getDemandeOperateurs()
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/coupon/edit/{id_coupon}', name: 'edit_coupon')]
    public function edit_coupon(ManagerRegistry $registry,
                                   ContratRepository $contratRepository,
                                   Request $request,
                                   MenuPermissionRepository $permissions,
                                   MenuRepository $menus,
                                   int $id_coupon,
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

                $coupon = $registry->getRepository(Coupon::class)->find($id_coupon);
                $mes_docs = array();
                $doc_op = $registry->getRepository(DocumentOperateur::class)->findBy(
                    [
                        'type_operateur'=>$registry->getRepository(TypeOperateur::class)->find(3),
                        'codeOperateur'=>$user->getCodeindustriel()
                    ]
                );
                foreach($doc_op as $doc){
                    $doc_coupon = $registry->getRepository(DocumentsCoupon::class)->findOneBy([
                        'code_coupon'=>$coupon,
                        'code_doc_op'=>$doc
                    ]);
                    if (!$doc_coupon){
                        $mes_docs[] = array(
                            'id'=>$doc->getId(),
                            'denomination'=>$doc->getCodeDocumentGrille()->getLibelleDocument()
                        );
                    }
                }
                return $this->render('transformation/coupon_app/edit.html.twig', [
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'contrats'=>$contratRepository->findBy(['code_usine'=>$user->getCodeindustriel()]),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'liste_parent'=>$permissions,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'groupe'=>$code_groupe,
                    'industriels'=>$registry->getRepository(Contrat::class)->findBy(['code_usine'=>$user->getCodeindustriel()],['created_at'=>'DESC']),
                    'docs_op'=>$user->getCodeindustriel()->getDemandeOperateurs(),
                    'le_coupon'=>$coupon,
                    'mes_docs'=>$mes_docs
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/coupon/all/{id_usine}', name: 'all_coupons')]
    public function all_coupons(ManagerRegistry $registry,
                               ContratRepository $contratRepository,
                               Request $request,
                               int $id_usine,
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
            if ($this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted("ROLE_MINEF") or $this->isGranted("ROLE_ADMIN"))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $array_coupons = array();

                if ($this->isGranted('ROLE_INDUSTRIEL')){
                    $mes_contrats = $user->getCodeindustriel()->getContrats();
                } else {
                    $mes_contrats = $registry->getRepository(Usine::class)->find($id_usine)->getContrats();
                }

                if ($mes_contrats){
                    foreach ($mes_contrats as $contrat){
                        $coupons = $registry->getRepository(Coupon::class)->findBy(['code_contrat'=>$contrat],['created_at'=>'DESC']);
                        foreach ($coupons as $coupon){
                            //$expire = $coupon->getCreatedAt() + $coupon->getNbJours();
                            $expire = date('d/m/Y', strtotime($coupon->getCreatedAt()->format('Y-m-d'). ' + '. $coupon->getNbJours() . ' days'));
                            //dd($expire);
                            $array_coupons[] = array(
                                'date_creation'=>$coupon->getCreatedAt()->format('d/m/Y'),
                                'id_coupon'=>$coupon->getId(),
                                'numero'=>$coupon->getCodeCoupon(),
                                'contrat'=>$coupon->getCodeContrat()->getNumeroContrat(),
                                'client'=>$coupon->getCodeContrat()->getRaisonSocialeClt(),
                                'contact'=>$coupon->getCodeContrat()->getContactPersonneRessource(),
                                'adresse'=>$coupon->getCodeContrat()->getAdresse(),
                                'expire_le'=>$expire,
                            );
                            rsort($array_coupons);
                        }
                    }
                }

                return  new JsonResponse(json_encode($array_coupons));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/coupon/create/{id_contrat}', name: 'create_coupon')]
    public function create_coupon(ManagerRegistry $registry,
                                   ContratRepository $contratRepository,
                                   Request $request,
                                   int $id_contrat = null,
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

                $mon_coupon  = array();

                $contrat = $registry->getRepository(Contrat::class)->find($id_contrat);
                //dd($contrat);
                if ($contrat){
                    $coupon = new Coupon();
                    $coupon->setNbJours(3);
                    $coupon->setCreatedAt(new \DateTimeImmutable());
                    $coupon->setCreatedBy($user);
                    $coupon->setCodeContrat($contrat);
                    $coupon->setFinalise(false);
                    $coupon->setCodeCoupon(strtoupper(
                        substr($user->getCodeindustriel()->getRaisonSocialeUsine(),0,1).
                        $this->utils->uniqidReal(8).
                        substr($user->getCodeindustriel()->getRaisonSocialeUsine(),-1)
                    ));
                    $registry->getManager()->persist($coupon);
                    $registry->getManager()->flush();

                    // Mise à jour log
                    $this->administrationService->save_action(
                        $user,
                        "COUPON",
                        "AJOUT COUPON",
                        new \DateTimeImmutable(),
                        "Le coupon  ". $coupon->getCodeCoupon() . " été créé par  l'agent " . $user . " de la structure [" . $user->getCodeindustriel()->getRaisonSocialeUsine() . " à l'endroit de son client " . $coupon->getCodeContrat()->getRaisonSocialeClt()
                    );

                    $my_cnt[] = array(
                        'code'=> 'SUCCESS',
                        'coupon'=>$coupon->getCodeCoupon(),
                        'id'=>$coupon->getId()
                    );
                } else {
                    $my_cnt[] = array(
                        'code'=> 'ERROR',
                        'coupon'=>'',
                        'id'=>0
                    );
                }

                return  new JsonResponse(json_encode($my_cnt));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/coupon/add-doc/{id_doc}/{id_coupon}', name: 'add_coupon_doc')]
    public function add_coupon_doc(ManagerRegistry $registry,
                                   ContratRepository $contratRepository,
                                   Request $request,
                                   int $id_doc = null,
                                   int $id_coupon = null,
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

                $doc_op = $registry->getRepository(DocumentOperateur::class)->find($id_doc);
                $coupon = $registry->getRepository(Coupon::class)->find($id_coupon);

                if ($doc_op && $coupon){
                    // Vérifie si le document est déja présent en base doc_coupon
                    $liste_doc_coupons = $registry->getRepository(DocumentsCoupon::class)->findOneBy([
                        'code_doc_op'=>$doc_op,
                        'code_coupon'=>$coupon
                    ]);
                    if (!$liste_doc_coupons){
                        $doc_coupon = new DocumentsCoupon();
                        $doc_coupon->setCodeCoupon($coupon);
                        $doc_coupon->setCodeDocOp($doc_op);

                        $registry->getManager()->persist($doc_coupon);
                        $registry->getManager()->flush();

                        // Mise à jour log
                        $this->administrationService->save_action(
                            $user,
                            "COUPON",
                            "AJOUT DOCUMENT",
                            new \DateTimeImmutable(),
                            "Le document  ". $doc_coupon->getCodeDocOp()->getCodeDocumentGrille()->getLibelleDocument() . " été ajouté au coupon  " . $doc_coupon->getCodeCoupon()->getCodeCoupon(). " par l'agent " . $user . " de la structure [" . $user->getCodeindustriel()->getRaisonSocialeUsine()
                        );

                        $my_cnt[] = array(
                            'code'=> 'SUCCESS'
                        );
                    }

                } else {
                    $my_cnt[] = array(
                        'code'=> 'ERROR'
                    );
                }

                return  new JsonResponse(json_encode($my_cnt));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/coupon/all-docs/{id_coupon}', name: 'all_docs_coupon')]
    public function all_docs_coupon(ManagerRegistry $registry,
                                   ContratRepository $contratRepository,
                                   Request $request,
                                   int $id_coupon = null,
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
                $my_cnt = array();
                $coupon = $registry->getRepository(Coupon::class)->find($id_coupon);
                $doc_op = $registry->getRepository(DocumentsCoupon::class)->findBy(
                    [
                        'code_coupon'=>$coupon
                    ]
                    );


                if ($doc_op){
                    foreach ($doc_op as $doc){

                        $my_cnt[] = array(
                            'code'=> 'SUCCESS',
                            'id_op'=>$doc->getId(),
                            'denomination'=> $doc->getCodeDocOp()->getCodeDocumentGrille()->getLibelleDocument(),
                            'expire_le'=>$doc->getCodeDocOp()->getDateExpiration()->format('d/m/Y'),
                            'fichier'=>$doc->getCodeDocOp()->getImageName()
                        );
                    }

                }

                return  new JsonResponse(json_encode($my_cnt));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
    #[Route('snvlt/coupon/list-doc/{id_coupon}', name: 'list_docs_coupon')]
        public function list_docs_coupon(ManagerRegistry $registry,
                                       ContratRepository $contratRepository,
                                       Request $request,
                                       int $id_coupon = null,
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

                    $coupon = $registry->getRepository(Coupon::class)->find($id_coupon);
                    $mes_docs = array();
                    $doc_op = $registry->getRepository(DocumentOperateur::class)->findBy(
                        [
                            'type_operateur'=>$registry->getRepository(TypeOperateur::class)->find(3),
                            'codeOperateur'=>$user->getCodeindustriel()
                        ]
                    );
                    foreach($doc_op as $doc){
                        $doc_coupon = $registry->getRepository(DocumentsCoupon::class)->findOneBy([
                            'code_coupon'=>$coupon,
                            'code_doc_op'=>$doc
                        ]);
                        if (!$doc_coupon){
                            $mes_docs[] = array(
                                'id'=>$doc->getId(),
                                'denomination'=>$doc->getCodeDocumentGrille()->getLibelleDocument()
                            );
                        }
                    }

                    return  new JsonResponse(json_encode($mes_docs));
                } else {
                    return $this->redirectToRoute('app_no_permission_user_active');
                }
            }
        }

    #[Route('snvlt/coupon/remove-doc/{id_doc}', name: 'remove_coupon_doc')]
    public function remove_coupon_doc(ManagerRegistry $registry,
                                   ContratRepository $contratRepository,
                                   Request $request,
                                   int $id_doc = null,
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

                $doc_op = $registry->getRepository(DocumentsCoupon::class)->find($id_doc);

                if ($doc_op){

                    $registry->getManager()->remove($doc_op);
                    $registry->getManager()->flush();

                    $my_cnt[] = array(
                        'code'=> 'SUCCESS'
                    );
                    // Mise à jour log
                    $this->administrationService->save_action(
                        $user,
                        "COUPON",
                        "RETRAIT DOCUMENT",
                        new \DateTimeImmutable(),
                        "Le document  ". $doc_op->getCodeDocOp()->getCodeDocumentGrille()->getLibelleDocument() . " été retiré du coupon  " . $doc_op->getCodeCoupon()->getCodeCoupon(). " par l'agent " . $user . " de la structure [" . $user->getCodeindustriel()->getRaisonSocialeUsine()
                    );
                } else {
                    $my_cnt[] = array(
                        'code'=> 'ERROR'
                    );
                }

                return  new JsonResponse(json_encode($my_cnt));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/coupon/finalise-coupon/{id_coupon}', name: 'finalise_coupon_doc')]
    public function finalise_coupon_doc(ManagerRegistry $registry,
                                   Request $request,
                                   int $id_coupon = null,
                                   UserRepository $userRepository,
                                   User $user = null): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $coupon = $registry->getRepository(Coupon::class)->find($id_coupon);

                if ($coupon){
                    $coupon->setFinalise(true);
                    $coupon->setUpdatedBy($user);
                    $coupon->setUpdatedAt(new \DateTime());
                    $registry->getManager()->persist($coupon);
                    $registry->getManager()->flush();

                    // Mise à jour log
                    $this->administrationService->save_action(
                        $user,
                        "COUPON",
                        "FINALISATION COUPON",
                        new \DateTimeImmutable(),
                        "Le coupon N° ". $coupon->getCodeCoupon() . " à l'endroit de " . $coupon->getCodeContrat()->getRaisonSocialeClt() . " a été cloturé et transmi par email par l'agent " . $user . " de la structure [" . $user->getCodeindustriel()->getRaisonSocialeUsine()
                    );

                    // envoi email au client
// generate a signed url and email it to the user

                    $email = (new TemplatedEmail())
                        ->from(new Address('snvlt@system2is.com', 'SNVLT INFOS'))
                        ->to($coupon->getCodeContrat()->getEmailPersonneRessource())
                        ->subject($this->translator->trans('SNVLT Coupon from your supplier'))
                        ->htmlTemplate('emails/coupon.html.twig')
                        ->context([
                            'coupon' => $coupon
                        ])
                    ;
                    $this->mailer->send($email);

                    $my_cnt[] = array(
                        'code'=> 'SUCCESS'
                    );


                } else {
                    $my_cnt[] = array(
                        'code'=> 'ERROR'
                    );
                }

                return  new JsonResponse(json_encode($my_cnt));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/coupon/search/{cpn}', name: 'search_coupon')]
    public function search_coupon(ManagerRegistry $registry,
                                        Request $request,
                                        string $cpn = null,
                                        UserRepository $userRepository,
                                        User $user = null): Response
    {
        $coupon = $registry->getRepository(Coupon::class)->findOneBy(['code_coupon'=>$cpn]);

        if ($coupon){
            $my_cnt[] = array(
                'code'=> 'SUCCESS'
            );

        } else {
            $my_cnt[] = array(
                'code'=> 'ERROR'
            );

        }
        return  new JsonResponse(json_encode($my_cnt));
    }

    #[Route('snvlt/coupon/infos/{cpn}', name: 'infos_coupon')]
    public function infos_coupon(ManagerRegistry $registry,
                                  Request $request,
                                  string $cpn = null,
                                  UserRepository $userRepository,
                                  User $user = null): Response
    {
        $coupon = $registry->getRepository(Coupon::class)->findOneBy(['code_coupon'=>$cpn]);
        return $this->render('coupon/infos.html.twig', [
            'coupon'=>$coupon
        ]);

    }

    #[Route('snvlt/doc_op/inf/{id_doc}/{id_coupon}', name: 'affiche_document_op')]
    public function affiche_document_op(ManagerRegistry $registry,
                                    Request $request,
                                    int $id_doc= null,
                                    int $id_coupon = null): Response
    {

                $my_cnt = array();
                //Vérifie le coupon

                $coupon = $registry->getRepository(Coupon::class)->find($id_coupon);
                $doc = $registry->getRepository(DocumentOperateur::class)->find($id_doc);
                if ($coupon && $doc){
                    $my_cnt[] = array(
                        'id_doc'=> $doc->getId(),
                        'doc_name'=>$doc->getCodeDocumentGrille()->getLibelleDocument(),
                        'fichier'=>$doc->getImageName()
                    );
                }


                return  new JsonResponse(json_encode($my_cnt));

    }
}
