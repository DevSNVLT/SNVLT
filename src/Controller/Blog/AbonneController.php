<?php

namespace App\Controller\Blog;

use App\Entity\Blog\Abonne;
use App\Form\Blog\AbonneType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AbonneController extends AbstractController
{
    #[Route('/add-abonne', name: 'add_abonne')]
    public function index(Abonne $abonne = null,
                          ManagerRegistry $doctrine,
                          Request $request,
                          SluggerInterface $slugger): Response
    {
        $new = false;
        $aujourdhui = new \DateTime();
        if(!$abonne){
            $new = true;

            $abonne = new Abonne();
            $abonne->setCreatedAt($aujourdhui);
        }

        $form = $this->createForm(AbonneType::class, $abonne);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ){

            $photo = $form->get('avatar')->getData();

            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photo->move(
                        $this->getParameter('abonnes_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $abonne->setAvatar($newFilename);
                $abonne->setCreatedAt($aujourdhui);
            }
            $message ="";
            if ($new){
                $message = "votre compte a été ajouté avec succès";
                //$personne->setCreatedBy($this->getUser());
            } else {
                $message = "votre compte a été mis à jour avec succès";
            }

            $manager = $doctrine->getManager();
            $manager->persist($abonne);
            $manager->flush();
            $request->getSession()->set('CURRENT_ABONNE_SESSION', $abonne);
            $this->addFlash('success', $message);
            return $this->redirectToRoute("app_portail");
        } else {
            return $this->render('blog/abonne/index.html.twig',[
                'form' =>$form->createView()
            ]);
        }
    }
}
