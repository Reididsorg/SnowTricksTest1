<?php


namespace App\Service\User;


use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Common\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserForgotPasswordManager
{
    protected UrlGeneratorInterface $urlGenerator;
    protected FlashBagInterface $flashBag;
    protected EntityManagerInterface $entityManager;
    protected UserRepository $userRepo;
    protected Mailer $mailer;
    protected EncoderFactoryInterface $encoderFactory;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag,
        EntityManagerInterface $entityManager,
        UserRepository $userRepo,
        Mailer $mailer,
        EncoderFactoryInterface $encoderFactory
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
        $this->entityManager = $entityManager;
        $this->userRepo = $userRepo;
        $this->mailer = $mailer;
        $this->encoderFactory = $encoderFactory;
    }

    public function askForNewPassword ($form, $tokenGenerator)
    {
        $email = $form->getData()->getEmail();

        $user = $this->userRepo->findOneBy(['email' => $email]);

        // Treatment only if user is found
        if ($user) {
            // Creation of the token
            $token = $tokenGenerator->generateToken();
            $user->setToken($token);
            // Token creation date
            $user->setPasswordRequestedAt(new \Datetime());
            $this->entityManager->flush();

            // Use of Mailer service to send email
            $bodyMail = $this->mailer->createBodyMail('user/user_mail_forgot_password.html.twig', [
                'user' => $user
            ]);
            $this->mailer->sendMessage($_ENV['MAILER_EXP'], $user->getEmail(), 'renouvellement du mot de passe', $bodyMail);
        }
    }

    public function resetPassword ($user) {
        $encoder = $this->encoderFactory->getEncoder(User::class);
        $passwordCrypted = $encoder->encodePassword($user->getPassword(), '');
        $user->setPassword($passwordCrypted);
        $user->setUpdatedAt(new \DateTime());

        // Set token to null
        $user->setToken(null);
        $user->setPasswordRequestedAt(null);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
