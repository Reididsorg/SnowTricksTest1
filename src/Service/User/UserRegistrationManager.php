<?php


namespace App\Service\User;


use App\Entity\User;
use App\Service\Common\Mailer;
use App\Service\Trick\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserRegistrationManager
{
    protected EntityManagerInterface $entityManager;
    protected EncoderFactoryInterface $encoderFactory;
    protected Mailer $mailer;
    protected FileUploader $fileUploader;

    public function __construct(
        EntityManagerInterface $entityManager,
        EncoderFactoryInterface $encoderFactory,
        Mailer $mailer,
        FileUploader $fileUploader
    ) {
        $this->entityManager = $entityManager;
        $this->encoderFactory = $encoderFactory;
        $this->mailer = $mailer;
        $this->fileUploader = $fileUploader;
    }

    public function createUser ($form, $tokenGenerator)
    {
        $userEntity = $form->getData();

        $encoder = $this->encoderFactory->getEncoder(User::class);
        $passwordCrypted = $encoder->encodePassword($userEntity->getPassword(), '');
        $userEntity->setPassword($passwordCrypted);

        // If photo is uploaded in the form
        //if (!is_null($form->getData()->getImageFileName())) {
        if (!($form->getData()->getImageFileName() === null)) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form['imageFileName']->getData();

            // Upload file to local file with a new unique name
            $imageFileName = $this->fileUploader->upload($imageFile, 'square');

            // Set the new filename
            $form->getData()->setImageFileName($imageFileName);

            // Set the alt
            $form->getData()->setImageAlt('Photo de profil de ' . $userEntity->getUserName());

            // Set the path
            $form->getData()->setImagePath($this->fileUploader->getAppUploadsDirectory());
        }

        $this->entityManager->persist($userEntity);

        // Creation of the token
        $token = $tokenGenerator->generateToken();
        $userEntity->setToken($token);
        // Token creation date
        $userEntity->setPasswordRequestedAt(new \Datetime());
        $this->entityManager->flush();

        // Use of Mailer service to send email
        $bodyMail = $this->mailer->createBodyMail('user/user_mail_registration.html.twig', [
            'user' => $userEntity
        ]);
        $this->mailer->sendMessage($_ENV['MAILER_EXP'], $userEntity->getEmail(), 'Activation de ton compte', $bodyMail);
    }
}
