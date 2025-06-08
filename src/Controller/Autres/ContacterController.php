<?php

namespace App\Controller\Autres;

use App\Entity\Autres\Contacter;
use App\Form\Autres\ContacterFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContacterController extends AbstractController
{
    #[Route('/nous-contacter', name: 'app_contacter')]
    public function index(ManagerRegistry $doctrine, Request $request, TranslatorInterface $translator): Response
    {
        $createdAt = new \DateTime();
        $message = new Contacter();

        $form = $this->createForm(ContacterFormType::class, $message);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){

            $manager = $doctrine->getManager();
            $message->setCreatdedAt($createdAt);
            $manager->persist($message);
            $message= $translator->trans('Votre message a été envoyé avec succès');

            $manager->flush();
            $this->addFlash('success',  $message);
            return $this->redirectToRoute("app_portail");
        } else {
            return $this->render('autres/contacter/index.html.twig',[
                'form' =>$form->createView()
            ]);
        }

    }
}
