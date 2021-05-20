<?php


namespace App\Controller;

use App\Entity\User;
use App\Forms\AccountType;
use App\Forms\ForgotPasswordType;
use App\Forms\ResetPasswordType;
use App\Forms\UpdatePasswordType;
use App\Forms\UserRegistrationType;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use App\Service\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

class SecurityController extends BaseController
{
    protected FormFactoryInterface $form;
    protected AuthenticationUtils $authUtils;
    protected Environment $templating;
    protected UrlGeneratorInterface $urlGenerator;
    protected FlashBagInterface $flashBag;
    protected EncoderFactoryInterface $encoderFactory;
    protected UserRepository $userRepo;
    protected string $targetDirectory;

    public function __construct(
        FormFactoryInterface $formFactory,
        AuthenticationUtils $authUtils,
        Environment $templating,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag,
        EntityManagerInterface $entityManager,
        EncoderFactoryInterface $encoderFactory,
        UserRepository $userRepo,
        string $targetDirectory
    ) {
        $this->formFactory = $formFactory;
        $this->authUtils = $authUtils;
        $this->templating = $templating;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
        $this->entityManager = $entityManager;
        $this->encoderFactory = $encoderFactory;
        $this->userRepo = $userRepo;
        $this->targetDirectory = $targetDirectory;
    }

    /**
     * @Route("/registration", name="app_registration")
     */
    public function registration(Request $request, FileUploader $fileUploader, Mailer $mailer, TokenGeneratorInterface $tokenGenerator)
    {
        $form = $this->formFactory->create(UserRegistrationType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userEntity = $form->getData();

            $encoder = $this->encoderFactory->getEncoder(User::class);
            $passwordCrypted = $encoder->encodePassword($userEntity->getPassword(), '');
            $userEntity->setPassword($passwordCrypted);

            // If photo is uploaded in the form
            if (!is_null($form->getData()->getImageFileName())) {
                /** @var UploadedFile $imageFile */
                $imageFile = $form['imageFileName']->getData();

                // Upload file to local file with a new unique name
                $imageFileName = $fileUploader->upload($imageFile);

                // Set the new filename
                $form->getData()->setImageFileName($imageFileName);

                // Set the alt
                $form->getData()->setImageAlt('Photo de profil de ' . $userEntity->getUserName());

                // Set the path
                $form->getData()->setImagePath($fileUploader->getAppUploadsDirectory());
            }

            $this->entityManager->persist($userEntity);
            //$this->entityManager->flush();

            // Creation of the token
            $token = $tokenGenerator->generateToken();
            $userEntity->setToken($token);
            // Token creation date
            $userEntity->setPasswordRequestedAt(new \Datetime());
            $this->entityManager->flush();

            // Use of Mailer service to send email
            $bodyMail = $mailer->createBodyMail('user/mail_confirm_account.html.twig', [
                'user' => $userEntity
            ]);
            $mailer->sendMessage('arsincitrusdev@gmail.com', $userEntity->getEmail(), 'Activation de ton compte', $bodyMail);

            $this->flashBag->add('success', 'Super ! Tu es enregistré :) Tu vas recevoir un courriel pour valider ton inscription');

            return new RedirectResponse(
                $this->urlGenerator->generate('app_homepage')
            );
        }

        return new Response(
            $this->templating->render(
                'user/registration.html.twig',
                [
                    'form' => $form->createView(),
                ]
            )
        );
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(): Response
    {
        $form = $this->formFactory->create(UserRegistrationType::class);
        $form->remove('email'); // Hide useless 'email' field
        $form->remove('imageFileName'); // Hide useless 'imageFileName' field
        $error = $this->authUtils->getLastAuthenticationError();

        return new Response(
            $this->templating->render(
                'security/login.html.twig',
                [
                    'form' => $form->createView(),
                    'error' => $error,
                    'lastUsername' => $this->authUtils->getLastUsername()
                ]
            )
        );
    }

    /**
     * @Route("/forgot-password", name="app_forgot_password")
     */
    public function forgotPassword(Request $request, Mailer $mailer, TokenGeneratorInterface $tokenGenerator)
    {
        $form = $this->formFactory->create(ForgotPasswordType::class);
        $form->remove('username'); // Hide useless 'username' field;
        $form->remove('password'); // Hide useless 'password' field;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

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
                $bodyMail = $mailer->createBodyMail('user/mail.html.twig', [
                    'user' => $user
                ]);
                //$mailer->sendMessage('from@email.com', $user->getEmail(), 'renouvellement du mot de passe', $bodyMail);
                $mailer->sendMessage('arsincitrusdev@gmail.com', $user->getEmail(), 'renouvellement du mot de passe', $bodyMail);
                $this->flashBag->add('success', 'Yop ! Un courriel vient de t\'être envoyé afin que tu puisses renouveller ton mot de passe. Le lien que tu recevras sera valide 24h.');

                return new RedirectResponse(
                    $this->urlGenerator->generate('app_login')
                );
            }

        }

        return new Response(
            $this->templating->render(
                'user/forgot_password.html.twig',
                [
                    'form' => $form->createView(),
                ]
            )
        );
    }

    // If request password is over 10 min (or value = null), return false
    private function isRequestInTime(\Datetime $passwordRequestedAt = null)
    {
        if ($passwordRequestedAt === null)
        {
            return false;
        }

        $now = new \DateTime();
        $interval = $now->getTimestamp() - $passwordRequestedAt->getTimestamp();

        $daySeconds = 60 * 10;
        $response = $interval > $daySeconds ? false : $reponse = true;
        return $response;
    }

    /**
     * @Route("/edit/account", name="app_edit_account")
     */
    public function editAccount(Request $request, FileUploader $fileUploader)
    {
        $user = $this->getUser();

        if (!$user) {
            $this->flashBag->add('danger', 'Tu as été déconnecté !');
            return new RedirectResponse(
                $this->urlGenerator->generate('app_homepage')
            );
        }
        else {
            $userOriginalImage = $user->getImageFileName();

            $form = $this->formFactory->create(AccountType::class, $user)
                ->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $formImage = $form['imageFileName']->getData();

                if ($formImage) {
                    /** @var UploadedFile $imageFile */
                    $imageFile = $formImage;

                    // Upload file to local file with a new unique name
                    $imageFileName = $fileUploader->upload($imageFile);

                    // Set the new filename
                    $form->getData()->setImageFileName($imageFileName);

                    // Set the alt
                    $form->getData()->setImageAlt('Photo de profil de ' . $user->getUserName());

                    // Set the path
                    $form->getData()->setImagePath($fileUploader->getAppUploadsDirectory());

                }

                $userEntity = $form->getData();

                // Remove old file
                $filePath = $this->targetDirectory . '/' . $userOriginalImage;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }

                // Save update time
                $userEntity->setUpdatedAt(new \DateTime());

                $this->entityManager->persist($userEntity);
                $this->entityManager->flush();

                $this->flashBag->add('success', 'Super ! Ton compte a été mis à jour avec succès ! :)');

                return new RedirectResponse(
                    $this->urlGenerator->generate('app_homepage')
                );
            }
        }

        return new Response(
            $this->templating->render(
                'user/edit_account.html.twig',
                [
                    'form' => $form->createView(),
                ]
            )
        );
    }


    /**
     * @Route("/confirmation/{token}", name="security_confirmation")
     */
    public function confirmationToken($token, Request $request)
    {
        // Get user with token
        $user = $this->userRepo->findOneBy(['token' => $token]);

        if ($user) {
            // Access forbidden if :
            // Token associated to member is null
            // Token in database and token in url are different
            // Token duration is over 10 minutes
            if ($user->getToken() === null || $token !== $user->getToken() || !$this->isRequestInTime($user->getPasswordRequestedAt()))
            {
                throw new AccessDeniedHttpException();
            }
            // Token is verified
            else {
                if ($user && !$user->isActive()) {
                    // Activate user
                    $user->setIsActive(true);
                    // Set token to null
                    $user->setToken(null);
                    $user->setPasswordRequestedAt(null);
                    $this->entityManager->flush();
                    $request->getSession()->getFlashBag()->add('success', "Et voilà ! Ton compte est activé ! :)");
                    return $this->redirectToRoute('app_login');
                }
            }
        }
    }


    /**
     * @Route("/{id}/{token}", name="resetting")
     */
    public function resetting($id, $token, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->userRepo->findOneBy(['id' => $id]);

        // Access forbidden if :
        // Token associated to member is null
        // Token in database and token in url are different
        // Token duration is over 10 minutes
        if ($user->getToken() === null || $token !== $user->getToken() || !$this->isRequestInTime($user->getPasswordRequestedAt()))
        {
            throw new AccessDeniedHttpException();
        }

        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $encoder = $this->encoderFactory->getEncoder(User::class);
            $passwordCrypted = $encoder->encodePassword($user->getPassword(), '');
            $user->setPassword($passwordCrypted);
            $user->setUpdatedAt(new \DateTime());

            // Set token to null
            $user->setToken(null);
            $user->setPasswordRequestedAt(null);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', "Yop ! Ton mot de passe a été renouvelé ! :)");

            return $this->redirectToRoute('app_login');

        }

        return $this->render('user/reset_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

}