<?php

namespace App\Controller;

use App\Controller\Services\AdministrationService;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct(private  AdministrationService $service, private ManagerRegistry $registry)
    {
    }

    #[Route(path: 'snvlt', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils,): Response
    {
       // dd($this->getUser());
        /* if ($this->getUser()) {
            return $this->redirect('https://localhost:8000/snvlt/admin');
         } else {*/
             // get the login error if there is one
             $error = $authenticationUtils->getLastAuthenticationError();
             // last username entered by the user
             $lastUsername = $authenticationUtils->getLastUsername();
            //$bloque = $authenticationUtils

             return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
         /*}*/
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {

        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
