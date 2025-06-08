<?php

namespace App\EventListeners;

use App\Entity\User;
use App\Events\Locale\AddChangeLocaleEvent;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class LocaleListener
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function onKernelRequest(RequestEvent $event)
    {

        $request = $event->getRequest();
        if ($request->getSession()->has("langue")){
            $locale = $request->getSession()->get("langue");

            $request->setLocale($locale);

        // Your logic to determine the user's locale

        }
    }
}