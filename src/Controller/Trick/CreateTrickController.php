<?php


namespace App\Controller\Trick;

use App\Controller\BaseController;
use App\Forms\TrickType;
use App\Service\Trick\TrickSaver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;


class CreateTrickController extends BaseController
{
    protected Environment $templating;
    protected FormFactoryInterface $formFactory;
    protected EntityManagerInterface $entityManager;
    protected UrlGeneratorInterface $urlGenerator;
    protected FlashBagInterface $flashBag;

    public function __construct(
        Environment $templating,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag,
        TrickSaver $trickSaver
    )
    {
        $this->templating = $templating;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
        $this->trickSaver = $trickSaver;
    }

    /**
     * @Route("/createTrick", name="app_create_trick")
     */
    public function createTrick(Request $request)
    {
        $form = $this->formFactory->create(TrickType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $formErrors = [];
            $trickToSave = [];

            // If no image : Error pointed on global form
            if (empty($form['images']->getData())) {
                $formErrors[] = 'images';
            }
            else {
                $trickToSave = $this->trickSaver->saveTrick($form, $formErrors);
                $formErrors = $trickToSave['formErrors'];
            }

            if ($formErrors) {
                foreach ($formErrors as $key => $fieldError) {
                    if ($fieldError === 'fileName') {
                        $form['images'][$key][$fieldError]->addError(new FormError('Champ Image obligatoire !'));
                    }
                    if ($fieldError === 'images') {
                        $form['images']->addError(new FormError('Il faut ajouter au moins une image !'));
                    }
                }
                return new Response(
                    $this->templating->render(
                        'tricks/create_trick.html.twig',
                        [
                            'form' => $form->createView(),
                        ]
                    )
                );
            }

            // Set user to trick
            $trickToSave['trickEntity']->setUser($this->getUser());

            $this->flashBag->add('success', 'super ! Le trick <strong>' . $trickToSave['trickEntity']->getName() . '</strong> a bien été enregistré !');

            $this->entityManager->persist($trickToSave['trickEntity']);
            $this->entityManager->flush();

            return new RedirectResponse(
                $this->urlGenerator->generate(
                    'app_display_trick',
                    [
                        'slug' => $trickToSave['trickEntity']->getSlug(),
                    ]
                )
            );
        }

        return new Response(
            $this->templating->render(
                'tricks/create_trick.html.twig',
                [
                    'form' => $form->createView(),
                ]
            )
        );
    }
}