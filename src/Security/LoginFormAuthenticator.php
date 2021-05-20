<?php


namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    protected UrlGeneratorInterface $urlGenerator;
    protected UserRepository $userRepository;
    protected UserPasswordEncoderInterface $passwordEncoder;
    protected FlashBagInterface $flashBag;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder,
        FlashBagInterface $flashBag
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->flashBag = $flashBag;
    }

    public function supports(Request $request)
    {
        return 'app_login' === $request->attributes->get('_route') && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'username' => $request->request->get('user_registration')['username'],
            'password' => $request->request->get('user_registration')['password'],
            'csrf_token' => $request->request->get('user_registration')['_token']
        ];

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['username'],
        );

        //dd($credentials);

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            $user = $userProvider->loadUserByUsername($credentials['username']);
        } catch (UsernameNotFoundException $e) {
            throw new CustomUserMessageAuthenticationException('Identifiants invalides');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $isValid = $this->passwordEncoder->isPasswordValid($user, $credentials['password']);

        if (!$isValid) {
            throw new CustomUserMessageAuthenticationException('Identifiants invalides');
        }

        if (!$user->isActive()) {
            throw new CustomUserMessageAuthenticationException('Ton compte n\'est pas encore activé. Active-la via le lien qui a été envoyé par courriel !');
        }

        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        //$this->flashBag->add('auth', sprintf('Welcome back %s', $token->getUser()->getUsername()));
        $this->flashBag->add('success', 'Bienvenue ' . ucfirst($token->getUser()->getUsername()) . ' !'); // Ugly concatenation more efficient in performance than sprintf()

        return new RedirectResponse($this->urlGenerator->generate('app_homepage'));
    }

    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate('app_login');
    }
}