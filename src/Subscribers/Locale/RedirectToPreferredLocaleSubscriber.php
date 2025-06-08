<?php

namespace App\Subscribers\Locale;

use http\Exception\UnexpectedValueException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use function Symfony\Component\String\u;

class RedirectToPreferredLocaleSubscriber
{
    /*private array $locales;
    private string $defaultLocale;

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        ?string $locales,
        ?string $defaultLocale= null
    )
    {
        $this->locales = explode('|', trim($locales));
        if(empty($this->locales)){
            throw new UnexpectedValueException('The list of supported locales must');
        }
        $this->defaultLocale = $defaultLocale ?:$this->locales[0];

        if(!\in_array($this->defaultLocale, $this->locales, true)){
            throw new UnexpectedValueException(sprintf('The default locale ("%s") must be one of "%s".',$this->defaultLocale, $locales ));
        }

        array_unshift($this->locales, $this->defaultLocale);
        $this->locales = array_unique($this->locales);

    }

    #[ArrayShape([KernelEvents::REQUEST => "string"])]
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest'
        ];
    }

    public function onKernelRequest(RequestEvent $event){
        $request = $event->getRequest();
        if(!$event->isMainRequest() || '/' !== $request->getPathInfo()){
            return;
        }

        $referrer= $request->headers->get(('referer'));
        if(null !== $referrer && u($referrer)->ignoreCase()->startsWith($request->getSchemeAndHttpHost())){
            return;
        }

        $prefferedLanguage = $request->getPreferredLanguage($this->locales);

        if ($prefferedLanguage !== $this->defaultLocale){
            $response = new RedirectResponse($this->urlGenerator->generate('defaulut_index', ['_locale'=>$prefferedLanguage]));
            $event->setResponse($response);
        }
    }*/
}