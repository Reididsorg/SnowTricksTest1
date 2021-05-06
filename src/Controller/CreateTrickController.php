<?php


namespace App\Controller;

use App\Forms\TrickType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
        FlashBagInterface $flashBag
    )
    {
        $this->templating = $templating;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
    }

    /**
     * @Route("/createTrick", name="app_create_trick")
     */
    public function createTrick(Request $request, FileUploader $fileUploader, string $targetDirectory)
    {
        $form = $this->formFactory->create(TrickType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $formErrors = [];

            // If no image : Error pointed on global form
            if (empty($form['images']->getData())) {
                $formErrors[] = 'images';
            }
            else {
                $trickEntity = $form->getData();
                $imageElements = $trickEntity->getImages();

                if ($imageElements) {
                    $i = 0;
                    foreach($imageElements  as $imageElement)
                    {
                        /** @var UploadedFile $imageFile */
                        $imageFile = $form['images'][$i]['fileName']->getData();

                        // If no fileName : Error pointed to fileName field
                        if (is_null($imageElement->getFileName())) {
                            $formErrors[] = 'fileName';
                        }

                        if ($imageFile) {
                            // Set first image of collection as main image
                            if($i == 0) {
                                $imageElement->setMain(true);
                            }
                            // Upload file to local file with a new unique name
                            $imageFileName = $fileUploader->upload($imageFile);
                            // Set the new filename
                            $imageElement->setFileName($imageFileName);
                            // Set the path
                            $imageElement->setPath($fileUploader->getAppUploadsDirectory());
                        }
                        $i++;
                    }
                }
            }

            if ($formErrors) {
                foreach ($formErrors as $key => $fieldError) {
                    if ($fieldError === 'fileName') {
                        $form['images'][$key][$fieldError]->addError(new FormError('Champ Image obligatoire !'));
                    }
                    if ($fieldError === 'images') {
                        $form['images']->addError(new FormError('Veuillez ajouter au moins une image !'));
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

            $this->flashBag->add('success', 'super ! Le trick <strong>' . $trickEntity->getName() . '</strong> a bien été enregistré !');

            $this->entityManager->persist($trickEntity);
            $this->entityManager->flush();

            return new RedirectResponse(
                $this->urlGenerator->generate(
                    'app_display_trick',
                    [
                        'slug' => $trickEntity->getSlug(),
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