<?php


namespace App\Controller\User;


use App\Controller\BaseController;
use App\Forms\User\UserRegistrationType;
use App\Repository\UserRepository;
use App\Service\User\UserRegistrationManager;
use App\Service\User\UserTokenChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Twig\Environment;

class UserRegistrationController extends BaseController
{
    protected FormFactoryInterface $form;
    protected Environment $templating;
    protected UrlGeneratorInterface $urlGenerator;
    protected FlashBagInterface $flashBag;
    protected EntityManagerInterface $entityManager;
    protected UserRepository $userRepo;
    protected UserRegistrationManager $userRegistrationManager;
    protected UserTokenChecker $userTokenChecker;

    public function __construct(
        FormFactoryInterface $formFactory,
        Environment $templating,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag,
        EntityManagerInterface $entityManager,
        UserRepository $userRepo,
        UserRegistrationManager $userRegistrationManager,
        UserTokenChecker $userTokenChecker
    ) {
        $this->formFactory = $formFactory;
        $this->templating = $templating;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
        $this->entityManager = $entityManager;
        $this->userRepo = $userRepo;
        $this->userRegistrationManager = $userRegistrationManager;
        $this->userTokenChecker = $userTokenChecker;
    }

    /**
     * @Route("/registration", name="app_registration")
     */
    public function registration(Request $request, TokenGeneratorInterface $tokenGenerator)
    {
        $form = $this->formFactory->create(UserRegistrationType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->userRegistrationManager->createUser($form, $tokenGenerator);

            $this->flashBag->add('success', 'Super ! Tu es enregistré :) Tu vas recevoir un courriel pour valider ton inscription');

            return new RedirectResponse(
                $this->urlGenerator->generate('app_homepage')
            );
        }

        return new Response(
            $this->templating->render(
                'user/user_registration.html.twig',
                [
                    'form' => $form->createView(),
                ]
            )
        );
    }

    /**
     * @Route("/activate_account/{token}", name="app_activate_account")
     */
    public function activateAccount($token, Request $request)
    {
        $user = $this->userRepo->findOneBy(['token' => $token]);

        if ($user) {
            // Check user access with token
            if ($this->userTokenChecker->checkUserAccessWithHisToken($user, $token) === 'accessDenied') {
                $this->flashBag->add('danger', 'Le délai du lien a expiré. Merci de renouveler ton inscription.');

                return new RedirectResponse(
                    $this->urlGenerator->generate('app_homepage')
                );
            }

            if ($user && !$user->isActive()) {
                // Activate user
                $user->setIsActive(true);
                // Set token to null
                $user->setToken(null);
                $user->setPasswordRequestedAt(null);
                $this->entityManager->flush();
                $request->getSession()->getFlashBag()->add('success', "Et voilà ! Ton compte est activé ! :) Tu peux désormais te connecter.");
                return $this->redirectToRoute('app_login');
            }
        }
        else {
            return $this->redirectToRoute('app_login');
        }
    }
}