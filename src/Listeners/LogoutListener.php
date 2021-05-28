<?php


namespace App\Listeners;


use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutListener
{
    private FlashBagInterface $flashBag;
    private TokenStorageInterface $tokenStorage;


    public function __construct(FlashBagInterface $flashBag, TokenStorageInterface $tokenStorage)
    {
        $this->flashBag = $flashBag;
        $this->tokenStorage = $tokenStorage;
    }

    public function onSymfonyComponentSecurityHttpEventLogoutEvent(LogoutEvent $logoutEvent): void
    {
        $this->flashBag->add('success', 'Au revoir '. ucfirst($this->tokenStorage->getToken()->getUsername()) .' ! A bientôt !');
    }
}