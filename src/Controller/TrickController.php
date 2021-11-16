<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\Picture;
use App\Entity\Video;
use App\Service\FileManager;
use App\Repository\TrickRepository;
use App\Repository\PictureRepository;
use App\Form\TrickType;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/trick')]
class TrickController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var FileManager
     */
    private FileManager $fileManager;

    public function __construct(EntityManagerInterface $entityManager, FileManager $fileManager)
    {
        $this->entityManager = $entityManager;
        $this->fileManager = $fileManager;
    }

    /**
    * @return \Symfony\Component\HttpFoundation\Response
    */
    #[Route('/new', name: 'app_trick_new')]
    public function new(Request $request): Response
    {
        $trick = new Trick();

        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $trick = $this->manageNewPicturesForms($trick, $form->get('pictures'), $this->fileManager);

            $trick = $this->manageVideosForms($trick, $form->get('videos'));

            $trick
                ->setCreatedAt(new \DateTimeImmutable())
                ->setAuthor($this->getUser());
 
            $this->entityManager->persist($trick);
            $this->entityManager->flush();

            $this->addflash('success', 'Le trick : ' . $trick->getTitle() . ' a bien été créé');

            return $this->redirectToRoute('app_trick_show', [
                'id' => $trick->getId(),
                'slug' => $trick->getSlug()
            ]);
        }

        return $this->render('trick/new.html.twig', [
            'form' => $form->createView(),
            'trick' => $trick,
            'current_nav' => 'new'
        ]);
    }

    /**
    * @param Trick $trick
    * @return \Symfony\Component\HttpFoundation\Response
    */
    #[Route('/edit/{id}', name: 'app_trick_edit')]
    public function edit(Trick $trick, Request $request): Response
    {
        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $trick = $this->manageEditPicturesForms($trick, $form->get('pictures'), $this->fileManager);
            $trick = $this->manageVideosForms($trick, $form->get('videos'));

            $trick->setUpdatedAt(new \DateTimeImmutable());

            $this->entityManager->persist($trick);
            $this->entityManager->flush();

            $this->addflash('success', 'Le trick : ' . $trick->getTitle() . ' a bien été modifié');

            return $this->redirectToRoute('app_trick_show', [
                'id' => $trick->getId(),
                'slug' => $trick->getSlug()
            ]);
        }

        return $this->render('trick/edit.html.twig', [
            'form' => $form->createView(),
            'trick' => $trick
        ]);
    }

    /**
    * @param Trick $trick
    * @return \Symfony\Component\HttpFoundation\Response
    */
    #[Route('/{id}/{slug}', name: 'app_trick_show', methods: ['GET','POST'])]
    public function show(Trick $trick, string $slug = null, Request $request): Response
    {
        $comment = new Comment;

        $form = $this->createForm(CommentType::class, $comment);
        
        if ((string)$trick->getSlug() !== $slug) {
            return $this->redirectToRoute('app_trick_show', [
                'id' => $trick->getId(),
                'slug' => $trick->getSlug()
            ]);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comment
                ->setCreatedAt(new \DateTimeImmutable())
                ->setAuthor($this->getUser())
                ->setTrick($trick);
            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            // reseting form
            $comment = new Comment();
            $form = $this->createForm(CommentType::class, $comment);

            $this->addflash('success', 'Votre message a bien été ajouté');
        }        
     
        return $this->render('trick/show.html.twig', [ 
            'trick' => $trick,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Trick $trick
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route('/delete/{id}', name: 'app_trick_delete', methods: ['DELETE'])]
    public function delete(Trick $trick, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->get('_token'))) {
            // Commenté pour tester le JS
            $pictures = $trick->getPictures();
            if ($pictures) {
                foreach ($pictures as $picture) {
                    $this->fileManager->removeFile($picture->getSource());
                }                
            }

            $this->entityManager->remove($trick);
            $this->entityManager->flush();

            $this->addflash('success', 'Le trick : ' . $trick->getTitle() . ' a bien été supprimé');
        }
        return $this->redirectToRoute('app_home');
    }

    /**
     * @param Picture $picture
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route('/picture/delete/{id}', name: 'app_trick_delete_picture', methods: ['DELETE'])]
    public function deletePicture(Picture $picture, Request $request): JsonResponse
    {
        return $this->deleteCollectionItem($picture, $request, $this->fileManager);
    }

    /**
     * @param Video $video
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route('/video/delete/{id}', name: 'app_trick_delete_video', methods: ['DELETE'])]
    public function deleteVideo(Video $video, Request $request): JsonResponse
    {
        return $this->deleteCollectionItem($video, $request);
    }

    /**
     * @param Video|Picture $collectionItem
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    private function deleteCollectionItem($collectionItem, Request $request): JsonResponse
    {
        $tokenName = 'delete_';
        $removeFunction = 'remove';
        if (get_class($collectionItem) === Video::class) {
            $tokenName .= 'video';
            $removeFunction .= 'Video';
        } elseif (get_class($collectionItem) === Picture::class) {
            $tokenName .= 'picture';
            $removeFunction .= 'Picture';
        }

        $data = json_decode($request->getContent(), true);

        if (array_key_exists('_token', $data) && $this->isCsrfTokenValid($tokenName . $collectionItem->getId(), $data['_token'])) {
            $trick = $collectionItem->getTrick();
            if ($trick) {
                // Commenté pour tester le JS
                $trick->$removeFunction($collectionItem);
                if (get_class($collectionItem) === Picture::class) {
                    $this->fileManager->removeFile($collectionItem->getSource());
                }
                $this->entityManager->remove($collectionItem);
                $this->entityManager->flush();
                return new JsonResponse(['success' => 1]);              
            }
        }
        return new JsonResponse(['error' => 'Invalid Token'], 400);
    }

    /**
     * @param Trick $trick
     * @param Form $picturesForms
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function manageEditPicturesForms(Trick $trick, Form $picturesForms): Trick
    {
        foreach ($picturesForms as $pictureForm) {
            // if empty subform
            if (null === $pictureForm->getData()->getId() && null === $pictureForm->get('file')->getData()) {
                $trick->removePicture($pictureForm->getData());
            } else {
                // if file is recieved
                if ($pictureForm->get('file')->getData()) {
                    // if a file already exists -> delete it
                    if ($pictureForm->getData()->getId()) {
                        $this->removePictureFile($pictureForm->getData(), $this->fileManager);
                    }
                    // uploading new file
                    $file = $pictureForm->get('file')->getData();
                    /** @var UploadedFile $pictureFile */
                    $newFilename = $this->fileManager->upload($file);
                    $pictureForm->getData()->setSource($newFilename);                   
                }
                if (!$pictureForm->get('alternateText')->getData()) {
                    $pictureForm->getData()->setAlternateText('image ' . $trick->getTitle());
                }                
            }
        }
        return $trick;
    }

    /**
     * @param Trick $trick
     * @param Form $picturesForms
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function manageNewPicturesForms(Trick $trick, Form $picturesForms): Trick
    {
        foreach ($picturesForms as $pictureForm) {
            // ignoring empty subforms
            if (null === $pictureForm->get('file')->getData()) {
                $trick->removePicture($pictureForm->getData());
            } else {
                $file = $pictureForm->get('file')->getData();
                /** @var UploadedFile $pictureFile */
                $newFilename = $this->fileManager->upload($file);   
                $pictureForm->getData()->setSource($newFilename);

                if (!$pictureForm->get('alternateText')->getData()) {
                    $pictureForm->getData()->setAlternateText('image ' . $trick->getTitle());
                }
            }                   
        }
        return $trick;
    }

    /**
     * @param Picture $id
     * @return void
     */
    private function removePictureFile(Picture $picture): void
    {
        $oldPicture = $this->entityManager->getRepository(Picture::class)->findOneBy(['id' => $picture->getId()]);
        $this->fileManager->removeFile($oldPicture->getSource());
    }

    /**
     * @param Trick $trick
     * @param Form $videosForms
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function manageVideosForms(Trick $trick, Form $videosForms): Trick
    {
        foreach ($videosForms as $videoForm) {
            if (null === $videoForm->getData()->getSource()) {
                $trick->removeVideo($videoForm->getData());
            }                
        }
        return $trick;
    }
}
