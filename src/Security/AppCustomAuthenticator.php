<?php

namespace App\Security;

use App\Controller\Services\AdministrationService;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppCustomAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(private UrlGeneratorInterface $urlGenerator, private AdministrationService $administrationService, private ManagerRegistry $registry)
    {
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);
        $request->getSession()->set('user_session', $email);

        //dd(str_replace("#","%23", $request->request->get('password', '')));
        return new Passport(
            new UserBadge($email),
            new PasswordCredentials(str_replace("#","%23", $request->request->get('password', ''))),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $email = $request->getSession()->get('user_session');
        //dd($email);
        $user = $this->registry->getRepository(User::class)->findOneBy(['email'=>$email]);
        $maj =  new \DateTimeImmutable();
        $this->administrationService->save_action(
            $user,
            "UTILISATEUR",
            "CONNEXION",
            $maj,
            "L'utilisateur ". $user . " vient de se connecter a la date du " . $maj->format("d/m/Y h:i:s")
        );
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }
       
        // For example:
        // return new RedirectResponse($this->urlGenerator->generate('app_tdb_admin'));4
        return new RedirectResponse($this->urlGenerator->generate('app_ouverture'));


        //throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
