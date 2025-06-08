<?php

namespace App\Controller\References;


use App\Entity\References\CircuitCommunication;
use App\Entity\References\DocumentOperateur;
use App\Entity\User;
use App\Events\References\AddDocumentOperateurEvent;
use App\Form\References\DocumentExportateurType;
use App\Form\References\DocumentIndustrielType;
use App\Form\References\DocumentOperateurType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\DemandeOperateurRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\CircuitCommunicationRepository;
use App\Repository\References\DocumentOperateurRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DocumentOperateurController extends AbstractController
{

    public function __construct(private TranslatorInterface $translator, private EventDispatcherInterface $dispatcher)
    {

    }

    #[Route('snvlt/ref/docop', name: 'ref_document_operateur')]
    public function listing(
        DocumentOperateurRepository $document_operateurs,
        CircuitCommunicationRepository $circuitCommunicationRepository,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        User $user = null,
        UserRepository $userRepository,
        ManagerRegistry $doctrine,
        Request $request,
        NotificationRepository $notification
        ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_EXPORTATEUR'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
            $titre = $this->translator->trans("Edit Operator Document");

                $codeoperateur = 0;
                $typeOperateur = $user->getCodeOperateur();
                if($typeOperateur->getId() == 2){
                    $codeoperateur = $user->getCodeexploitant()->getId();
                }elseif($typeOperateur->getId() == 3){
                    $codeoperateur = $user->getCodeindustriel()->getId();
                }elseif($typeOperateur->getId() == 4){
                    $codeoperateur = $user->getCodeExportateur()->getId();
                }

                return $this->render('references/document_operateur/index.html.twig', [
                    'liste_document_operateurs' => $document_operateurs->findBy(['codeOperateur'=>$codeoperateur,'type_operateur'=>$typeOperateur],['created_at'=>'DESC']),
                    'mes_circuits'=>$circuitCommunicationRepository,
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                    'groupe'=>$code_groupe,
                    'titre'=>$titre,
                    'liste_parent'=>$permissions
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
            }
        }


        #[Route('snvlt/ref/admin/docop', name: 'admin_doc_operateur')]
        public function admin_doc_operateur(
            DocumentOperateurRepository $document_operateurs,
            CircuitCommunicationRepository $circuitCommunicationRepository,
            MenuRepository $menus,
            MenuPermissionRepository $permissions,
            User $user = null,
            UserRepository $userRepository,
            ManagerRegistry $doctrine,
            Request $request,
            NotificationRepository $notification
        ): Response
        {
            if(!$request->getSession()->has('user_session')){
                return $this->redirectToRoute('app_login');
            } else {
                if ($this->isGranted('ROLE_ADMIN') or  $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMINISTRATIF'))
                {
                    $user = $userRepository->find($this->getUser());
                    $code_groupe = $user->getCodeGroupe()->getId();
                    $titre = $this->translator->trans("Edit Operator Document");


                    return $this->render('references/document_operateur/admin_op.html.twig', [
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'groupe'=>$code_groupe,
                        'titre'=>$titre,
                        'liste_parent'=>$permissions
                    ]);
                } else {
                    return $this->redirectToRoute('app_no_permission_user_active');
                }
            }
        }

    #[Route('snvlt/ref/docoperateur/edit/{id_document_operateur?0}', name: 'document_operateur.edit')]
    public function editDocumentOperateur(
        DocumentOperateur $document_operateur = null,
        ManagerRegistry $doctrine,
        Request $request,
        DocumentOperateurRepository $document_operateurs,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        NotificationRepository $notification,
        GroupeRepository $groupeRepository,
        int $id_document_operateur,
        User $user = null,
        UserRepository $userRepository,): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_EXPORTATEUR'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();


        $dateMAJ = new \DateTimeImmutable();

        $titre = $this->translator->trans("Edit Operator Document");
        $document_operateur = $document_operateurs->find($id_document_operateur);
        //dd($document_operateur);
        if(!$document_operateur){
            $new = true;
            $document_operateur = new DocumentOperateur();
            $titre = $this->translator->trans("Add Operator Document");
        }

            $new = false;
            if(!$document_operateur){
                $new = true;
                $document_operateur = new DocumentOperateur();
            }
            if($user->getCodeOperateur()->getId() == 2){
                $form = $this->createForm(DocumentOperateurType::class, $document_operateur);
            } else if($user->getCodeOperateur()->getId() == 3) {
                $form = $this->createForm(DocumentIndustrielType::class, $document_operateur);
            }else {
                $form = $this->createForm(DocumentExportateurType::class, $document_operateur);
            }


            $form->handleRequest($request);

            if ( $form->isSubmitted() && $form->isValid() ){
                $document_operateur->setStatut("EN ATTENTE");
                $document_operateur->setCreatedAt($dateMAJ);
                $document_operateur->setCreatedBy($user);
                $document_operateur->setDemandeurId($user);

                //Affectation du type Operateur
                $typeOperateur = $user->getCodeOperateur();
                $document_operateur->setTypeOperateur($typeOperateur);

                //Recherche et affectation du code opérateur
                $codeoperateur = 0;
                if($typeOperateur->getId() == 2){
                    $codeoperateur = $user->getCodeexploitant()->getId();
                }elseif($typeOperateur->getId() == 3){
                    $codeoperateur = $user->getCodeindustriel()->getId();
                }elseif($typeOperateur->getId() == 4){
                    $codeoperateur = $user->getCodeExportateur()->getId();
                }
                $document_operateur->setCodeOperateur($codeoperateur);

                $manager = $doctrine->getManager();
                $manager->persist($document_operateur);
                $manager->flush();

                //Crer l'evenement pour la génération de circuit de validation
                $addDocumentOperateurEvent = new AddDocumentOperateurEvent($document_operateur);

                //Dispatcher l'evenement
                $this->dispatcher->dispatch($addDocumentOperateurEvent, AddDocumentOperateurEvent::ADD_DOCUMENT_OPERATEUR_EVENT);


                $this->addFlash('success',$this->translator->trans("Operator document has been updated successfully"));
                return $this->redirectToRoute("ref_document_operateur");
            } else {
                return $this->render('references/document_operateur/add-document_operateur.html.twig',[
                    'form' =>$form->createView(),
                    'titre'=>$titre,
                    'liste_document_operateurs' => $document_operateurs->findAll(),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'liste_parent'=>$permissions
                ]);
            }
        /*}*/
    } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
    }
    }

    #[Route('snvlt/ref/admin/docop/del/{id_doc}', name: 'delete_doc_op')]
    public function delete_doc_op(
        int $id_doc,
        UserRepository $userRepository,
        ManagerRegistry $doctrine,
        Request $request
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or
                $this->isGranted('ROLE_INDUSTRIEL') or
                $this->isGranted('ROLE_EXPORTATEUR') or
                $this->isGranted('ROLE_ADMIN') or
                $this->isGranted('ROLE_MINEF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $reponse = array();
                $doc = $doctrine->getRepository(DocumentOperateur::class)->find($id_doc);
                if($doc){
                    $circuits =  $doctrine->getRepository(CircuitCommunication::class)->findBy(['code_document_operateur'=>$doc]);
                    foreach ($circuits as $circuit){
                        $doctrine->getManager()->remove($circuit);
                    }
                    $doctrine->getManager()->remove($doc);
                    $doctrine->getManager()->flush();

                    $reponse[] = array(
                        'html'=>'success'
                    );
                }
                return  new JsonResponse(json_encode($reponse));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/admin/op/doc', name: 'liste_docs_op')]
    public function liste_docs_op(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DemandeOperateurRepository $demandes): Response
    {

        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF') )
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                return $this->render('references/document_operateur/admin_op.html.twig',
                    [
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                        'groupe'=>$code_groupe,
                        'liste_parent'=>$permissions
                    ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/grille/doc-op/{id_doc}', name: 'gl_doc_operateur')]
    public function gl_doc_operateur(

        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        User $user = null,
        int $id_doc,
        UserRepository $userRepository,
        ManagerRegistry $doctrine,
        Request $request
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')) {

                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();


                $reponse = array();

                $doc = $doctrine->getRepository(DocumentOperateur::class)->find((int) $id_doc);
                //dd($doc);
                $reponse[] = array(
                    'id'=>$doc->getId(),
                    'document'=>$doc->getCodeDocumentGrille()->getLibelleDocument(),
                    'date_etablissement'=>$doc->getDateEtablissement()->format('Y-m-d'),
                    'date_expiration'=>$doc->getDateExpiration()->format('Y-m-d')
                );

                return  new JsonResponse(json_encode($reponse));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/docop/op/edit/{data}', name: 'edit_doc_legalite_op')]
    public function edit_doc_legalite_op(
        Request $request,
        UserRepository $userRepository,
        User $user = null,
        string $data,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF'))
            {
                $user = $userRepository->find($this->getUser());

                //$page_brh = $pages_brh->find($id_page);
                $arraydata = json_decode($data);
                if($data){
                    $doc = $registry->getRepository(DocumentOperateur::class)->find((int) $arraydata->id);

                    if ($doc){
                        //Decoder le JSON BRH
                        $arraydata = json_decode($data);

                        //dd($arraydata->numero_lignepagebrh);
                        $date_jour = new \DateTime();

                        $date_etablissement =  $arraydata->date_etablissement;
                        $date_expiration =  $arraydata->date_expiration;

                        $doc->setDateEtablissement(\DateTime::createFromFormat('Y-m-d', $date_etablissement));
                        $doc->setDateExpiration(\DateTime::createFromFormat('Y-m-d', $date_expiration));
                        $doc->setUpdatedAt(new \DateTimeImmutable());
                        $doc->setUpdatedBy($user);

                        $registry->getManager()->persist($doc);
                        $registry->getManager()->flush();

                        $reponse = true;

                    } else {
                        $reponse = false;
                    }
                    return  new JsonResponse(json_encode($reponse));
                }

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
}
