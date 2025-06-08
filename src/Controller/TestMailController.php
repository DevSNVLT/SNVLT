<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class TestMailController extends AbstractController
{
    #[Route('/mail', name: 'app_mail')]
    public function index(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('Infos SNVLT <snvlt@system2is.com>')
            ->to('aziz.ndia@outlook.com')
            ->subject('Test Email!')
            ->text('Sending emails is fun again!')
            ->html('<p>Bonsoir TRABI. Ceci est un test emailing dans la mise en place du SNVLT! Depuis l\'application WEB</p>');

        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $e->getTrace();            // some error prevented the email sending; display an
            // error message or try to resend the message
        }
        dd($mailer);
        //mail('aziz.ndia@outlook.com','Test Aziz!','<p>Bonsoir TRABI. Ceci est un test emailing dans la mise en place du SNVLT! Depuis l\'application WEB</p>');
        return $this->redirectToRoute('app_login');
    }
}
