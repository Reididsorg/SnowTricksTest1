<?php


namespace App\Service\User;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserEditionManager
{
    protected EntityManagerInterface $entityManager;
    protected string $targetDirectory;

    public function __construct(
        EntityManagerInterface $entityManager,
        string $targetDirectory
    ) {
        $this->entityManager = $entityManager;
        $this->targetDirectory = $targetDirectory;
    }

    public function editUser ($form, $fileUploader, $user, $userOriginalImage)
    {
        $formImage = $form['imageFileName']->getData();

        if ($formImage) {
            /** @var UploadedFile $imageFile */
            $imageFile = $formImage;

            // Upload file to local file with a new unique name
            $imageFileName = $fileUploader->upload($imageFile);

            // Set the new filename
            $form->getData()->setImageFileName($imageFileName);

            // Set the alt
            $form->getData()->setImageAlt('Photo de profil de ' . $user->getUserName());

            // Set the path
            $form->getData()->setImagePath($fileUploader->getAppUploadsDirectory());

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