<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Contracts\Translation\TranslatorInterface;

class LanguesController extends AbstractController

{
    public function __construct(
        private TranslatorInterface $translator,
        private LocaleSwitcher $localeSwitcher)
    {
    }

    #[Route('/change_langue/{locale}', name: 'app_change_langue')]
    public function index(string $locale, Request $request)
    {

        $this->localeSwitcher->setLocale($locale);

        $request->getSession()->set('_locale', $locale);
        $request->setDefaultLocale($locale);
        //dd($this->localeSwitcher);


        return $this->redirectToRoute('app_portail');
    }


    #[Route('/changeLocales', name: 'changeLocales')]
    public function changeLanguage(RequestStack $requestStack, Request $request): Response
    {


        $lang = $request->request->get('lang');
        $request->getSession()->set("langue",$lang);


            return new JsonResponse(['status' => 'ok']);


    }

}
