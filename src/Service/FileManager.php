<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileManager
{
    private $targetDirectory;
    private $slugger;

    public function __construct($targetDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    /**
     * Moves given uploaded file to the target directory (defined in services.yaml) 
     * then returns its safe filename
     * 
     * @param UploadedFile $file
     * @return string $filename
     */
    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . md5(uniqid()) . '.' . $file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            return "error-moving-" . $fileName;
        }

        return $fileName;
    }

    /**
     * Removes given filename if the file does exist
     * 
     * @param $filename
     * @return void
     */
    public function removeFile(string $filename): void
    {
        if (file_exists($this->getTargetDirectory() . DIRECTORY_SEPARATOR . $filename)) {
            unlink($this->getTargetDirectory() . DIRECTORY_SEPARATOR . $filename);
        }      
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}
