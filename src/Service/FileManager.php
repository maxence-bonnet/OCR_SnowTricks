<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileManager
{
    private $picturesDirectory;
    private $avatarsDirectory;
    private $slugger;

    public function __construct($picturesDirectory, $avatarsDirectory, SluggerInterface $slugger)
    {
        $this->picturesDirectory = $picturesDirectory;
        $this->avatarsDirectory = $avatarsDirectory;
        $this->slugger = $slugger;
    }

    /**
     * Moves given uploaded file to the target directory (defined in services.yaml)
     * then returns its safe filename. Specify option 'avatar' for users avatar uploads
     * 
     * @param UploadedFile $file
     * @param string $purpose option to separate user avatar and trick pictures
     * @return string $filename
     */
    public function upload(UploadedFile $file, ?string $purpose = null): string
    {
        $directory = $this->picturesDirectory;

        if ($purpose === 'avatar') {
            $directory = $this->avatarsDirectory;
        }

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . md5(uniqid()) . '.' . $file->guessExtension();

        try {
            $file->move($directory, $fileName);
        } catch (FileException $e) {
            return "error-moving-" . $fileName;
        }

        return $fileName;
    }

    /**
     * Removes given filename if the file does exist. Specify option 'avatar' for users avatar
     * 
     * @param $filename
     * @param string $purpose option to separate user avatar and trick pictures
     * @return void
     */
    public function removeFile(string $filename, ?string $purpose = null): void
    {
        $directory = $this->picturesDirectory;

        if ($purpose === 'avatar') {
            $directory = $this->avatarsDirectory;
        }

        if (file_exists($directory . DIRECTORY_SEPARATOR . $filename)) {
            unlink($directory . DIRECTORY_SEPARATOR . $filename);
        }      
    }
}
