<?php


namespace App\Service;


use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private string $targetDirectory;
    private string $appUploadsDirectory;

    public function __construct(string $targetDirectory, string $appUploadsDirectory)
    {
        $this->targetDirectory = $targetDirectory;
        $this->appUploadsDirectory = $appUploadsDirectory;
    }

    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        $newFilename = $this->getAppUploadsDirectory() . $fileName;
        return $newFilename;

    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

    public function getAppUploadsDirectory()
    {
        return $this->appUploadsDirectory;
    }
}