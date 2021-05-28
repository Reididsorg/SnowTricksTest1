<?php


namespace App\Controller\Trick;


use App\Controller\BaseController;
use App\Repository\TrickRepository;
use App\Service\Trick\TrickEditManager;
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

class EditTrickController extends BaseController
{
    protected Environment $templating;
    protected FormFactoryInterface $formFactory;
    protected EntityManagerInterface $entityManager;
    protected UrlGeneratorInterface $urlGenerator;
    protected FlashBagInterface $flashBag;
    protected TrickRepository $trickRepository;
    protected TrickEditManager $trickEditManager;

    public function __construct(
        Environment $templating,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag,
        TrickRepository $trickRepository,
        TrickEditManager $trickEditManager
    )
    {
        $this->templating = $templating;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
        $this->trickRepository = $trickRepository;
        $this->trickEditManager = $trickEditManager;
    }

    /**
     * @Route("/edit/tricks/{slug}", name="app_edit_trick")
     */
    public function editTrick($slug, Request $request)
    {
        // Get trick in order to compare with form
        $trick = $this->trickRepository->findOneBySlug($slug);

        $dataToCompare = $this->trickEditManager->getDataToCompare($trick, $request);

        if ($dataToCompare['form']->isSubmitted() && $dataToCompare['form']->isValid()) {

            $trickToUpdate = $this->trickEditManager->updateTrick(
                    $trick,
                    $dataToCompare['form'],
                    $dataToCompare['exist3FirstFields'],
                    $dataToCompare['form3FirstFields'],
                    $dataToCompare['existAlts'],
                    $dataToCompare['existFileNames'],
                    $dataToCompare['existNames'],
                    $dataToCompare['formExistImages'],
                    $dataToCompare['existVideoIds'],
                    $dataToCompare['existVideoNames'],
                    $dataToCompare['formExistVideos'],
                    $dataToCompare['existVideoUrls']
                );

            if ($trickToUpdate['formErrors']) {

                //foreach ($formErrors as $key => $fieldError) {
                foreach ($trickToUpdate['formErrors'] as $key => $fieldError) {
                    if ($fieldError === 'fileName') {
                        $dataToCompare['form']['images'][$key][$fieldError]->addError(new FormError(
                            'Champ Image obligatoire !')
                        );
                    }
                    if ($fieldError === 'images') {
                        $dataToCompare['form']['images']->addError(new FormError('Veuillez ajouter au moins une image !'));
                    }
                    if ($fieldError === 'noChange') {
                        $dataToCompare['form']['images']->addError(new FormError(
                            'Veuillez effectuer au moins une modification pour déclencher l\'enregistrement !')
                        );
                    }
                }

                return new Response(
                    $this->templating->render(
                        'tricks/edit_trick.html.twig',
                        [
                            'form' => $dataToCompare['form']->createView(),
                        ]
                    )
                );
            }

            $this->flashBag->add('success',
                'super ! Le trick <strong>' . $trickToUpdate['trickEntity']->getName() . '</strong> a été mis à jour !');

            return new RedirectResponse(
                $this->urlGenerator->generate(
                    'app_display_trick',
                    [
                        'slug' => $trickToUpdate['trickEntity']->getSlug(),
                    ]
                )
            );
        }

        return new Response(
            $this->templating->render(
                'tricks/edit_trick.html.twig',
                [
                    'form' => $dataToCompare['form']->createView(),
                ]
            )
        );
    }
}