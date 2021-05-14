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

        dump($file);

        $originalFileName = $file->getFileName();
        $originalDirectory = $file->getPath();
        $originalFilePath = $file->getPathname();
        $newDirectory = $this->getTargetDirectory();
        $new_width = 1688;
        $new_height = 949;

        dump($originalFileName);
        dump($originalDirectory);
        dump($originalFilePath);
        dump($newDirectory);

//exit;

        $mime = getimagesize($originalFilePath);
        dump($mime);

        if($mime['mime']=='image/png') {
            $src_img = imagecreatefrompng($originalFilePath);
        }
        if($mime['mime']=='image/jpg' || $mime['mime']=='image/jpeg' || $mime['mime']=='image/pjpeg') {
            $src_img = imagecreatefromjpeg($originalFilePath);
        }

        $old_x = imageSX($src_img);
        $old_y = imageSY($src_img);

        if($old_x > $old_y)
        {
            $thumb_w = $new_width;
            $thumb_h = $old_y*($new_height/$old_x);
        }

        if($old_x < $old_y)
        {
            $thumb_w  = $old_x*($new_width/$old_y);
            $thumb_h = $new_height;
        }

        if($old_x == $old_y)
        {
            $thumb_w = $new_width;
            $thumb_h = $new_height;
        }

        $dst_img = ImageCreateTrueColor($thumb_w,$thumb_h);

        imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y);


        // New save location
        $new_thumb_loc = $originalFilePath;

        dump($new_thumb_loc);

        //exit;

        if($mime['mime']=='image/png') {
            $result = imagepng($dst_img,$new_thumb_loc,8);
        }
        if($mime['mime']=='image/jpg' || $mime['mime']=='image/jpeg' || $mime['mime']=='image/pjpeg') {
            $result = imagejpeg($dst_img,$new_thumb_loc,80);
        }


        //exit;






        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);

        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
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