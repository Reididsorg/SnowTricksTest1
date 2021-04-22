<?php


namespace App\Controller;

use App\Entity\User;
use App\Forms\UserRegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

class SecurityController extends BaseController
{
    protected FormFactoryInterface $form;
    protected AuthenticationUtils $authUtils;
    protected Environment $templating;
    protected UrlGeneratorInterface $urlGenerator;
    protected FlashBagInterface $flashBag;

    public function __construct(
        FormFactoryInterface $formFactory,
        AuthenticationUtils $authUtils,
        Environment $templating,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag,
        EntityManagerInterface $entityManager,
        EncoderFactoryInterface $encoderFactory
    ) {
        $this->formFactory = $formFactory;
        $this->authUtils = $authUtils;
        $this->templating = $templating;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
        $this->entityManager = $entityManager;
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(): Response
    {
        $form = $this->formFactory->create(UserRegistrationType::class);
        $form->remove('email'); // Hide useless 'email' field
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
     * @Route("/registration", name="app_registration")
     */
    public function registration(Request $request)
    {
        $form = $this->formFactory->create(UserRegistrationType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userEntity = $form->getData();

            $encoder = $this->encoderFactory->getEncoder(User::class);
            //dd($userEntity);
            $passwordCrypted = $encoder->encodePassword($userEntity->getPassword(), '');
            $userEntity->setPassword($passwordCrypted);

            $this->entityManager->persist($userEntity);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'Super ! Ton inscription a bien été enregistrée :)');

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
}