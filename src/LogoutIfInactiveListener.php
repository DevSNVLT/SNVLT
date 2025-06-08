<?php

namespace App;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class LogoutIfInactiveListener
{

    private SessionInterface $session;
    private RouterInterface $router;
    private TokenStorageInterface $token;
    private int $maxIdleTime = 0;

    public function __construct(SessionInterface $session, RouterInterface $router, TokenStorageInterface $token, int $maxIdleTime)
    {
        $this->session = $session;
        $this->router = $router;
        $this->token = $token;
        $this->maxIdleTime = $maxIdleTime;
    }

    public function onKernelRequest(RequestEvent $event)
        {
            if (HttpKernelInterface::MASTER_REQUEST != $event->getRequestType()) {
                return;
            }
            dd($this->maxIdleTime);
            if ($this->maxIdleTime > 900) {

                $this->session->start();
                $lapse = time() - $this->session->getMetadataBag()->getLastUsed();

                if ($lapse > $this->maxIdleTime) {
                    $event->setResponse(new RedirectResponse($this->router->generate('app_logout')));
                }
            }
    }
}