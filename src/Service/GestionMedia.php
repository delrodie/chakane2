<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\AsciiSlugger;

class GestionMedia
{
    private $mediaSlide;
    private $mediaProduit;
    private $mediaMarque;
    private $mediaCollection;

    public function __construct(
        $slideDirectory, $produitDirectory, $marqueDirectory, $collectionDirectory
    )
    {
        $this->mediaSlide = $slideDirectory;
        $this->mediaProduit = $produitDirectory;
        $this->mediaMarque = $marqueDirectory;
        $this->mediaCollection = $collectionDirectory;
    }

    /**
     * @param $form
     * @param object $entity
     * @param string $entityName
     * @return void
     */
    public function media($form, object $entity, string $entityName): void
    {
        // Gestion des médias
        $mediaFile = $form->get('media')->getData();
        if ($mediaFile){
            $media = $this->upload($mediaFile, $entityName);

            if ($entity->getMedia()){
                $this->removeUpload($entity->getMedia(), $entityName);
            }

            $entity->setMedia($media);
        }
    }


    /**
     * @param UploadedFile $file
     * @param $media
     * @return string
     */
    public function upload(UploadedFile $file, $media = null): string
    {
        // Initialisation du slug
        $slugify = new AsciiSlugger();

        $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugify->slug($originalFileName);
        $newFilename = $safeFilename.'-'.Time().'.'.$file->guessExtension();

        // Deplacement du fichier dans le repertoire dedié
        try {
            if ($media === 'slide') $file->move($this->mediaSlide, $newFilename);
            elseif ($media === 'marque') $file->move($this->mediaMarque, $newFilename);
            elseif ($media === 'collection') $file->move($this->mediaCollection, $newFilename);
            elseif ($media === 'produit') $file->move($this->mediaProduit, $newFilename);
            else $file->move($this->mediaSlide, $newFilename);
        }catch (FileException $e){

        }

        return $newFilename;
    }

    /**
     * Suppression de l'ancien media sur le server
     *
     * @param $ancienMedia
     * @param null $media
     * @return bool
     */
    public function removeUpload($ancienMedia, $media = null): bool
    {
        if ($media === 'slide') unlink($this->mediaSlide.'/'.$ancienMedia);
        elseif ($media === 'marque') unlink($this->mediaMarque.'/'.$ancienMedia);
        elseif ($media === 'collection') unlink($this->mediaCollection.'/'.$ancienMedia);
        elseif ($media === 'produit') unlink($this->mediaProduit.'/'.$ancienMedia);
        else return false;

        return true;
    }

}