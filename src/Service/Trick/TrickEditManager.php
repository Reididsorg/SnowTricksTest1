<?php


namespace App\Service\Trick;


use App\Forms\TrickType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class TrickEditManager
{
    protected Environment $templating;
    protected FormFactoryInterface $formFactory;
    protected EntityManagerInterface $entityManager;
    protected UrlGeneratorInterface $urlGenerator;
    protected FlashBagInterface $flashBag;
    protected TrickRepository $trickRepository;
    protected FileUploader $fileUploader;

    public function __construct(
        Environment $templating,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag,
        TrickRepository $trickRepository,
        FileUploader $fileUploader
    )
    {
        $this->templating = $templating;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
        $this->trickRepository = $trickRepository;
        $this->fileUploader = $fileUploader;
    }


    public function getDataToCompare ($trick, $request) {
        $trickCopy = $trick;

        // Get array of the 3 first fields
        $exist3FirstFields = [$trick->getName(), $trick->getDescription(), $trick->getCategory()->getId()];

        // Get Image collection in order to compare with form
        $existImages = []; // Arrays of Image collection
        $i = 0;
        foreach($trickCopy->getImages() as $trickImage)
        {
            $existImages[$i]['id'] = $trickImage->getId();
            $existImages[$i]['fileName'] = $trickImage->getFileName();
            $existImages[$i]['name'] = $trickImage->getName();
            $existImages[$i]['alt'] = $trickImage->getAlt();
            $existImages[$i]['path'] = $trickImage->getPath();
            $existImages[$i]['main'] = $trickImage->isMain();
            $i++;
        }

        $existIds = array_map(function ($item) {
            return $item['id'];
        }, $existImages);
        $existNames = array_map(function ($item) {
            return $item['name'];
        }, $existImages);
        $existAlts = array_map(function ($item) {
            return $item['alt'];
        }, $existImages);
        $existFileNames = array_map(function ($item) {
            return $item['fileName'];
        }, $existImages);

        // Get Video collection in order to compare with form
        $existVideos = []; // Arrays of Video collection
        $i = 0;
        foreach($trickCopy->getVideos() as $trickVideo)
        {
            $existVideos[$i]['id'] = $trickVideo->getId();
            $existVideos[$i]['name'] = $trickVideo->getName();
            $existVideos[$i]['url'] = $trickVideo->getUrl();
            $i++;
        }
        $existVideoIds = array_map(function ($item) {
            return $item['id'];
        }, $existVideos);
        $existVideoNames = array_map(function ($item) {
            return $item['name'];
        }, $existVideos);
        $existVideoUrls = array_map(function ($item) {
            return $item['url'];
        }, $existVideos);

        // Get form
        $form = $this->formFactory->create(TrickType::class, $trick)
            ->handleRequest($request);

        // Get array of the 3 first fields
        $form3FirstFields = [$form->getData()->getName(),
            $form->getData()->getDescription(),
            $form->getData()->getCategory()->getId()];

        // Get real Image existing fileNames and set missing fileName of Image existing elements to original Image value
        $formImagesBeforeSubmission = $form->getData()->getImages()->toArray();
        $formExistImages = []; // fill array of form existing images
        foreach ($formImagesBeforeSubmission as $key => $image) {
            $existImagesIndex = array_search($image->getId(), $existIds); // index in existing images array
            if ($existImagesIndex !== false) {
                $formExistImages[$key] = $image; // fill $formExistingImages
                // Set missing fileName of Image existing elements to original Image value
                $form->getData()->getImages()[$key]->setFileName($existFileNames[$existImagesIndex]);
            }
        }

        // Get real Video existing ids
        $formVideosBeforeSubmission = $form->getData()->getVideos()->toArray();
        $formExistVideos = []; // fill array of form existing videos
        foreach ($formVideosBeforeSubmission as $key => $video) {
            $existVideosIndex = array_search($video->getId(), $existVideoIds); // index in existing videos array
            if ($existVideosIndex !== false) {
                $formExistVideos[$key] = $video; // fill $formExistingIVideos
            }
        }

        return [
            'form' => $form,
            'exist3FirstFields' => $exist3FirstFields,
            'form3FirstFields' => $form3FirstFields,
            'existAlts' => $existAlts,
            'existFileNames' => $existFileNames,
            'existNames' => $existNames,
            'formExistImages' => $formExistImages,
            'existVideoIds' => $existVideoIds,
            'existVideoNames' => $existVideoNames,
            'formExistVideos' => $formExistVideos,
            'existVideoUrls' => $existVideoUrls
        ];


    }

    public function updateTrick (
        $trick,
        $form,
        $exist3FirstFields,
        $form3FirstFields,
        $existAlts,
        $existFileNames,
        $existNames,
        $formExistImages,
        $existVideoIds,
        $existVideoNames,
        $formExistVideos,
        $existVideoUrls
    )
    {
        $formErrors = [];

        // If no image : Error pointed on global form
        if (empty($form['images']->getData()->toArray())) {
            $formErrors[] = 'images';
        }
        else {

            // Compare existing Images with form images
            $formImages = $form->getData()->getImages()->toArray();
            $formFileNames = array_map(function ($item) { // Get form images
                return $item->getFileName();
            }, $formImages);
            $imagesToDelete = array_diff($existFileNames, $formFileNames);
            $imagesToAdd = array_diff($formFileNames, $existFileNames);
            $imagesToUpdate = array_intersect($existFileNames, $formFileNames);
            $imagesToIgnore = []; // Separate elements to ignore and elements to update
            foreach ($imagesToUpdate as $key => $element) {
                $existImagesIndex = array_search($element, $existFileNames); // index in existing images array
                if ($existNames[$existImagesIndex] === $formExistImages[$key]->getName()
                    && $existAlts[$existImagesIndex] === $formExistImages[$key]->getAlt()
                    && $existFileNames[$existImagesIndex] === $formExistImages[$key]->getFileName()
                ) {
                    $imagesToIgnore[] = $element;
                    unset($imagesToUpdate[$key]);
                }
            }

            // Treatment of images elements
            $imageElements = $form->getData()->getImages();

            if ($imageElements) {
                $i = 0;
                foreach($imageElements  as $key => $imageElement)
                {
                    // Delete
                    if (in_array($imageElement->getFileName(), $imagesToDelete)) {
                        $form->getData()->removeImage($imageElement);
                    }

                    // Ignore, Add or update
                    else {

                        // Set first image of collection as main image
                        if($i == 0) {
                            $form->getData()->getImages()[$key]->setMain(true);
                        }

                        // If not Ignore
                        if (!in_array($imageElement->getFileName(), $imagesToIgnore)) {
                            /** @var UploadedFile $imageFile */
                            $imageFile = $form['images'][$key]['fileName']->getData();

                            // If no fileName : Error pointed to fileName field
                            if (is_null($imageElement->getFileName())) {
                                $formErrors[$key] = 'fileName';
                            }

                            if ($imageFile) {
                                // Upload file to local file with a new unique name
                                $imageFileName = $this->fileUploader->upload($imageFile, '16/9');
                                // Set the new filename
                                $form->getData()->getImages()[$key]->setFileName($imageFileName);
                                // Set the path
                                $form->getData()->getImages()[$key]->setPath($this->fileUploader->getAppUploadsDirectory());
                                // Set the trick
                                $form->getData()->getImages()[$key]->setTrick($trick);
                            }
                            if (in_array($imageElement->getFileName(), $imagesToUpdate)) {
                                // Set the update date
                                $form->getData()->getImages()[$key]->setUpdatedAt(new \DateTime());
                            }
                        }
                    }
                    $i++;
                }
            }

            // Compare existing Videos with form videos
            $formVideos = $form->getData()->getVideos()->toArray();
            $formVideoIds = array_map(function ($item) { // Get form videos
                return $item->getId();
            }, $formVideos);
            $videosToDelete = array_diff($existVideoIds, $formVideoIds);
            $videosToAdd = array_diff($formVideoIds, $existVideoIds);
            $videosToUpdate = array_intersect($existVideoIds, $formVideoIds);
            $videosToIgnore = []; // Separate elements to ignore and elements to update
            foreach ($videosToUpdate as $key => $element) {
                $existVideosIndex = array_search($element, $existVideoIds); // index in existing videos array
                if ($existVideoNames[$existVideosIndex] === $formExistVideos[$key]->getName()
                    && $existVideoUrls[$existVideosIndex] === $formExistVideos[$key]->getUrl()
                ) {
                    $videosToIgnore[] = $element;
                    unset($videosToUpdate[$key]);
                }
            }

            // Treatment of Videos elements
            $videoElements = $form->getData()->getVideos()->toArray();
            if ($videoElements) {
                foreach($videoElements  as $key => $videoElement)
                {
                    // Delete
                    if (in_array($videoElement->getUrl(), $videosToDelete)) {
                        $form->getData()->removeVideo($videoElement);
                    }
                    // Ignore
                    elseif (in_array($videoElement->getUrl(), $videosToIgnore)) {
                        // No treatment
                    }
                    // Add or update
                    else {
                        // Set the name
                        $form->getData()->getVideos()[$key]->setName($videoElement->getName());
                        // Set the url
                        $form->getData()->getVideos()[$key]->setUrl($videoElement->getUrl());
                        // Set the trick
                        $form->getData()->getVideos()[$key]->setTrick($trick);
                        // Set the update date
                        $form->getData()->getVideos()[$key]->setUpdatedAt(new \DateTime());
                    }
                }
            }

            // If form has no change : Error pointed on global form
            if (empty(array_diff($exist3FirstFields, $form3FirstFields))
                && empty($imagesToDelete)
                && empty($imagesToAdd)
                && empty($imagesToUpdate)
                && empty($videosToDelete)
                && empty($videosToAdd)
                && empty($videosToUpdate)
            ) {
                $formErrors[] = 'noChange';
            }
        }

        // Updated form is now ready for persist/flush
        $trickEntity = $form->getData();
        $trickEntity->setUpdatedAt(new \DateTime());
        $this->entityManager->persist($trickEntity);
        $this->entityManager->flush();

        return ['formErrors' => $formErrors, 'trickEntity' => $trickEntity];
    }
}