<?php


namespace App\Service\User;


use App\Service\Trick\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserEditionManager
{
    protected EntityManagerInterface $entityManager;
    protected string $targetDirectory;
    protected FileUploader $fileUploader;

    public function __construct(
        EntityManagerInterface $entityManager,
        string $targetDirectory,
        FileUploader $fileUploader
    ) {
        $this->entityManager = $entityManager;
        $this->targetDirectory = $targetDirectory;
        $this->fileUploader = $fileUploader;
    }

    public function editUser ($form, $user, $userOriginalImage)
    {
        $formImage = $form['imageFileName']->getData();

        if ($formImage) {
            /** @var UploadedFile $imageFile */
            $imageFile = $formImage;

            // Upload file to local file with a new unique name
            $imageFileName = $this->fileUploader->upload($imageFile, 'square');

            // Set the new filename
            $form->getData()->setImageFileName($imageFileName);

            // Set the alt
            $form->getData()->setImageAlt('Photo de profil de ' . $user->getUserName());

            // Set the path
            $form->getData()->setImagePath($this->fileUploader->getAppUploadsDirectory());

            // Remove old file
            $filePath = $this->targetDirectory . '/' . $userOriginalImage;
            if (!is_null($userOriginalImage) && file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $userEntity = $form->getData();

        // Save update time
        $userEntity->setUpdatedAt(new \DateTime());

        $this->entityManager->persist($userEntity);
        $this->entityManager->flush();

        return true;
    }
}