<?php

namespace App\Controller\Administration;

use App\Entity\Administration\DocStatsGen;
use App\Entity\User;
use App\Repository\Administration\DocStatsGenRepository;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\PageDocGenRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Endroid\QrCodeBundle\Response\QrCodeResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DocStatsGenController extends AbstractController
{
    private $translator;
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }


    #[Route('/snvlt/ref/docgen/{id_doc}', name: 'docgen.show')]
    public function affiche_doc(ManagerRegistry $registry,
                                PageDocGenRepository $page,
                                Request $request,
                                MenuPermissionRepository $permissions,
                                MenuRepository $menus,
                                GroupeRepository $groupeRepository,
                                UserRepository $userRepository,
                                User $user = null,
                                NotificationRepository $notification,
                                int $id_doc): Response
    {
        $session = $request->getSession();
        if (!$session->has("user_session")){
            $this->addFlash('error', $this->translator->trans('You must log in first to access SNVLT'));
            return $this->redirectToRoute('app_login');
        } else {
            $user = $userRepository->find($this->getUser());
            $code_groupe = $user->getCodeGroupe()->getId();

        $doctrine = $registry->getRepository(DocStatsGen::class)->find($id_doc);

            //$response = new QrCodeResponse($doctrine->getUniqueDoc());
        return $this->render('administration/doc_stats_gen/index.html.twig', [
            //'qr_code_doc' => $response,
            'document'=>$doctrine,
            'liste_menus'=>$menus->findOnlyParent(),
            "all_menus"=>$menus->findAll(),
            'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
            'pages'=>$page->findBy(['code_doc_gen'=>$id_doc]),
            'liste_parent'=>$permissions,
            'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
            'groupe'=>$code_groupe
            ]);
        }
    }
    #[Route('/snvlt/secauth/{uniqdoc?0}', name: 'secure_auth_doc')]
    public function verifieDocument(ManagerRegistry $registry, DocStatsGen $docStatsGen = null, $uniqdoc){
        $docStatsGen = $registry->getManager()->getRepository(DocStatsGen::class)->findBy(['uniqueDoc'=>$uniqdoc]);
        if ($docStatsGen){
            return $this->render('recherche/docs_gen/trouve.html.twig', [
                'document'=>$docStatsGen
            ]);
        } else {
            return $this->render('recherche/docs_gen/non-trouve.html.twig');
        }
    }
}
