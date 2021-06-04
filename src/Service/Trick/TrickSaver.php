<?php


namespace App\Service\Trick;


use Symfony\Component\HttpFoundation\File\UploadedFile;

class TrickSaver
{
    protected FileUploader $fileUploader;

    public function __construct(
        FileUploader $fileUploader
    )
    {
        $this->fileUploader = $fileUploader;
    }

    public function saveTrick ($form, $formErrors)
    {
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
                    $imageFileName = $this->fileUploader->upload($imageFile, '16/9');
                    // Set the new filename
                    $imageElement->setFileName($imageFileName);
                    // Set the path
                    $imageElement->setPath($this->fileUploader->getAppUploadsDirectory());
                }
                $i++;
            }
        }

        return ['trickEntity' => $trickEntity, 'formErrors'=> $formErrors];
    }
}