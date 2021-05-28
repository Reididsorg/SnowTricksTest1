<?php


namespace App\Service\Trick;


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

    public function upload(UploadedFile $file, $givenRatio): string
    {
        $originalFilePath = $file->getPathname();
        $mime = getimagesize($originalFilePath);

        if($mime['mime']=='image/png') {
            $tempImg = imagecreatefrompng($originalFilePath);
        }
        if($mime['mime']=='image/jpg' || $mime['mime']=='image/jpeg' || $mime['mime']=='image/pjpeg') {
            $tempImg = imagecreatefromjpeg($originalFilePath);
        }

        if ($givenRatio === 'square') {
            $ratioToAdapt = 1;
        }
        if ($givenRatio === '16/9') {
            $ratioToAdapt = 16 / 9;
        }

        $ratio = imagesx($tempImg) / imagesy($tempImg);

        // If aspect ratio is not 16/9 : Crop image to adapt to 16/9
        if(!(round($ratio, 2) === round($ratioToAdapt, 2))) {
            if($ratio < $ratioToAdapt) {
                $width = imagesx($tempImg);
                $height = (imagesx($tempImg) / 16) * 9;
            }
            else if($ratio > $ratioToAdapt) {
                $width = (imagesy($tempImg) / 9) * 16;
                $height = imagesy($tempImg);
            }

            $croppedImg = imagecrop($tempImg, ['x' => 0, 'y' => 0, 'width' => $width, 'height' => $height]);

            if ($croppedImg !== FALSE) {
                // New save location
                $new_thumb_loc = $originalFilePath;

                if($mime['mime']=='image/png') {
                    $result = imagepng($croppedImg,$new_thumb_loc,8);
                }
                if($mime['mime']=='image/jpg' || $mime['mime']=='image/jpeg' || $mime['mime']=='image/pjpeg') {
                    $result = imagejpeg($croppedImg,$new_thumb_loc,80);
                }
                //imagedestroy($croppedImg);
            }
        }

        // Set unique name to file
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        // Move renamed file from /tmp to application
        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }
        return $fileName;
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