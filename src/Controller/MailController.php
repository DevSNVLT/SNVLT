<?php

namespace App\Controller;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailController extends AbstractController
{
    #[Route('/mailsnvlt', name: 'app_mailsnvlt')]
    public function index(MailerInterface $mailer): Response
    {
        $email = (new TemplatedEmail())
            ->from('sav@system2is.com')
            ->to('aziz.ndia@outlook.com')
            ->subject('Ceci est un test')
            ->text('Test de démarrage')
            ->htmlTemplate('emails/mail.html')
            ->context([
            'nom'=>'N\'DIA',
            'prenom'=>'Abdoul Aziz',
            'job'=>'Développeur symfony',
            'presentation'=>'Je suis un développeur Symfony'
        ]);

        $mailer->send($email);
        return $this->render('mail/index.html.twig', [
            'controller_name' => 'MailController',
        ]);
    }
}
