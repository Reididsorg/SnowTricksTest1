<?php


namespace App\Controller\User;


use App\Controller\BaseController;
use App\Forms\User\UserEditType;
use App\Service\User\UserEditionManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class UserEditController extends BaseController
{
    protected FormFactoryInterface $form;
    protected Environment $templating;
    protected UrlGeneratorInterface $urlGenerator;
    protected FlashBagInterface $flashBag;
    protected UserEditionManager $userEditionManager;

    public function __construct(
        FormFactoryInterface $formFactory,
        Environment $templating,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag,
        UserEditionManager $userEditionManager
    ) {
        $this->formFactory = $formFactory;
        $this->templating = $templating;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
        $this->userEditionManager = $userEditionManager;
    }

    /**
     * @Route("/edit/account", name="app_edit_account")
     */
    public function editAccount(Request $request)
    {
        $user = $this->getUser();

        if (!$user) {
            $this->flashBag->add('danger', 'Tu as été déconnecté !');
            return new RedirectResponse(
                $this->urlGenerator->generate('app_homepage')
            );
        }
        else {
            $userOriginalImageName = $user->getImageFileName();
            $originalUser = [$user->getImageFileName(), $user->getUsername(), $user->getEmail()];

            $form = $this->formFactory->create(UserEditType::class, $user)
                ->handleRequest($request);

            if ($form->getData()->getImageFileName() === null) {
                $user->setImageFileName($userOriginalImageName);
            }

            $formUser = [$form->getData()->getImageFileName(), $form->getData()->getUsername(), $form->getData()->getEmail()];

            $differences = array_diff($formUser, $originalUser);

            if (!empty($differences)) {
                if ($form->isSubmitted() && $form->isValid()) {
                    $userToEdit = $this->userEditionManager->editUser($form, $user, $userOriginalImageName);
                    if ($userToEdit) {
                        $this->flashBag->add('success', 'Super ! Ton compte a été mis à jour avec succès ! :)');

                        return new RedirectResponse(
                            $this->urlGenerator->generate('app_edit_account')
                        );
                    }
                }
            }
        }

        return new Response(
            $this->templating->render(
                'user/user_edit.html.twig',
                [
                    'form' => $form->createView(),
                ]
            )
        );
    }
}