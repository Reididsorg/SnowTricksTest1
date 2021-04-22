<?php


namespace App\Controller;


use App\Entity\Image;
use App\Forms\TrickType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
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
     * @Route("/createTrick", name="app_createTrick")
     */
    public function createTrick(Request $request, FileUploader $fileUploader, string $targetDirectory)
    {
        $form = $this->formFactory->create(TrickType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $trickEntity = $form->getData();

            /*
             * Get UploadedFile object thanks to the form (Don't forget to change the ImageType field name and add "'mapped' => false" option.
             */
            /** @var UploadedFile $mainImageFile */
            $mainImageFile = $form['mainImage']['path']->getData();
            //dd($mainImageFile);

            /*
             * Get UploadedFile object thanks to the form or Request object
             * In case I don't change ImageType field name and add "'mapped' => false" option
             */
            //$mainImageFile = $request->files->get('trick')['mainImage']['path'];

            if ($mainImageFile) {
                $mainImageFileName = $fileUploader->upload($mainImageFile);
                $mainImage = new Image();
                $mainImage->setName($trickEntity->getMainImage()->getName());
                $mainImage->setAlt($trickEntity->getMainImage()->getAlt());
                $mainImage->setPath($mainImageFileName);
                $trickEntity->setMainImage($mainImage);
                $mainImage->setTrick($trickEntity);
                $mainImage->setMain(true);
            }

            $imageElements = $form['images']->getData();
            if ($imageElements) {
                $i = 0;
                foreach($imageElements  as $imageElement)
                {

                    /** @var UploadedFile $imageFile */
                    $imageFile = $form['images'][$i]['path']->getData();

                    if ($imageFile) {
                        $imageFileName = $fileUploader->upload($imageFile);
                        $imageElement->setPath($imageFileName);
                    }
                    $i++;
                }
            }

            $this->flashBag->add('success', 'super ! Le trick <strong>' . $trickEntity->getName() . '</strong> a bien été enregistré !');

            $this->entityManager->persist($trickEntity);
            $this->entityManager->flush();


            return new RedirectResponse(
                $this->urlGenerator->generate(
                    'app_displayTrick',
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