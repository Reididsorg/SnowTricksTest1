<?php


namespace App\Controller\Security;

use App\Controller\BaseController;
use App\Forms\Security\UserLoginType;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormFactoryInterface;
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
    protected EncoderFactoryInterface $encoderFactory;
    protected UserRepository $userRepo;
    protected string $targetDirectory;

    public function __construct(
        FormFactoryInterface $formFactory,
        AuthenticationUtils $authUtils,
        Environment $templating
    ) {
        $this->formFactory = $formFactory;
        $this->authUtils = $authUtils;
        $this->templating = $templating;
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(): Response
    {
        $form = $this->formFactory->create(UserLoginType::class);
        $error = $this->authUtils->getLastAuthenticationError();

        return new Response(
            $this->templating->render(
                'security/user_login.html.twig',
                [
                    'form' => $form->createView(),
                    'error' => $error,
                    'lastUsername' => $this->authUtils->getLastUsername()
                ]
            )
        );
    }
}