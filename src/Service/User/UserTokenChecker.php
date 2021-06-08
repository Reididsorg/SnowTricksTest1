<?php


namespace App\Service\User;


use Symfony\Component\HttpFoundation\RedirectResponse;

class UserTokenChecker
{
    // If request password is over 10 min (or value = null), return false
    public function isRequestInTime(\Datetime $passwordRequestedAt = null)
    {
        if ($passwordRequestedAt === null)
        {
            return false;
        }

        $now = new \DateTime();
        $interval = $now->getTimestamp() - $passwordRequestedAt->getTimestamp();

        $daySeconds = 60 * 10;
        $response = $interval > $daySeconds ? false : true;
        return $response;
    }

    // Check user access with token
    public function checkUserAccessWithHisToken ($user, $token)
    {
        if ($user->getToken() === null || $token !== $user->getToken() || !$this->isRequestInTime($user->getPasswordRequestedAt()))
        {
            //throw new AccessDeniedHttpException('Le délai du lien a expiré. Merci de renouveler ta demande.');
            return 'accessDenied';
        }
    }
}