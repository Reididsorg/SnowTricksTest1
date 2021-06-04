<?php


namespace App\Listeners;


use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LogoutListener
{
    private FlashBagInterface $flashBag;
    private TokenStorageInterface $tokenStorage;


    public function __construct(FlashBagInterface $flashBag, TokenStorageInterface $tokenStorage)
    {
        $this->flashBag = $flashBag;
        $this->tokenStorage = $tokenStorage;
    }

    public function onSymfonyComponentSecurityHttpEventLogoutEvent(): void
    {
        $this->flashBag->add('success', 'Au revoir '. ucfirst($this->tokenStorage->getToken()->getUsername()) .' ! A bientôt !');
    }
}