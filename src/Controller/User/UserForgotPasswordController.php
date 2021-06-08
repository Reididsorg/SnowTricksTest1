<?php


namespace App\Controller\User;


use App\Controller\BaseController;
use App\Forms\User\UserForgotPasswordType;
use App\Forms\User\UserResetPasswordType;
use App\Repository\UserRepository;
use App\Service\User\UserForgotPasswordManager;
use App\Service\User\UserTokenChecker;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Twig\Environment;

class UserForgotPasswordController extends BaseController
{
    protected FormFactoryInterface $form;
    protected Environment $templating;
    protected UrlGeneratorInterface $urlGenerator;
    protected FlashBagInterface $flashBag;
    protected UserRepository $userRepo;
    protected userForgotPasswordManager $userForgotPasswordManager;
    protected UserTokenChecker $userTokenChecker;

    public function __construct(
        FormFactoryInterface $formFactory,
        Environment $templating,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag,
        UserRepository $userRepo,
        userForgotPasswordManager $userForgotPasswordManager,
        UserTokenChecker $userTokenChecker
    ) {
        $this->formFactory = $formFactory;
        $this->templating = $templating;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
        $this->userRepo = $userRepo;
        $this->UserForgotPasswordManager = $userForgotPasswordManager;
        $this->userTokenChecker = $userTokenChecker;
    }

    /**
     * @Route("/ask-new-password", name="app_ask_new_password")
     */
    public function askNewPassword(Request $request, TokenGeneratorInterface $tokenGenerator)
    {
        $form = $this->formFactory->create(UserForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->UserForgotPasswordManager->askForNewPassword($form, $tokenGenerator);

            $this->flashBag->add('success', 'Yop ! Un courriel vient de t\'être envoyé afin que tu puisses renouveler ton mot de passe. Le lien que tu recevras sera valide 24h.');

            return new RedirectResponse(
                $this->urlGenerator->generate('app_homepage')
            );
        }

        return new Response(
            $this->templating->render(
                'user/user_forgot_password.html.twig',
                [
                    'form' => $form->createView(),
                ]
            )
        );
    }

    /**
     * @Route("/reset-password/{id}/{token}", name="app_reset_password")
     */
    public function resetPassword($id, $token, Request $request)
    {
        $user = $this->userRepo->findOneBy(['id' => $id]);

        // Check user access with token
        if ($this->userTokenChecker->checkUserAccessWithHisToken($user, $token) === 'accessDenied') {
            $this->flashBag->add('danger', 'Le délai du lien a expiré. Merci de renouveler ta demande de mot de passe.');

            return new RedirectResponse(
                $this->urlGenerator->generate('app_homepage')
            );
        }

        $form = $this->createForm(UserResetPasswordType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->UserForgotPasswordManager->resetPassword($user);

            $request->getSession()->getFlashBag()->add('success', "Yop ! Ton mot de passe a été renouvelé ! :)");

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/user_reset_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}