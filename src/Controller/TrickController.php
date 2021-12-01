<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\Picture;
use App\Service\FileManager;
use App\Repository\CommentRepository;
use App\Form\TrickType;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
    #[IsGranted('ROLE_VERIFIED_USER', statusCode: 403, message: 'Vous devez être connecté et avoir une adresse email vérifiée pour accéder à cette page')]
    public function new(Request $request): Response
    {
        $trick = new Trick();

        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $trick = $this->manageNewPicturesForms($trick, $form->get('pictures'));
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
    #[IsGranted('ROLE_VERIFIED_USER', statusCode: 403, message: 'Vous devez être connecté et avoir une adresse email vérifiée pour accéder à cette page')]
    public function edit(Trick $trick, Request $request): Response
    {
        // allows access for trick author (or admin) only
        try {
            $this->denyAccessUnlessGranted('edit', $trick, 'Vous n\'avez pas l\'autorisation de modifier ce trick');
        } catch (AccessDeniedException $e) {
            $this->addFlash('danger', $e->getMessage());
            return $this->redirectToRoute('app_home');
        }

        if ($trick->getAuthor() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        // customized pictures tracking to ensure file deletion
        $originalPictures = new ArrayCollection();
        foreach ($trick->getPictures() as $picture) {
            $originalPictures->add($picture);
        }

        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            foreach ($originalPictures as $picture) {
                if (false === $trick->getPictures()->contains($picture)) {
                    $this->fileManager->removeFile($picture->getSource());
                }
            }

            $trick = $this->manageEditPicturesForms($trick, $form->get('pictures'));
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
    public function show(Trick $trick, string $slug = null, Request $request, CommentRepository $commentRepository): Response
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

        $limit = 10;
        $page = (int)$request->get('page', 1) ?: 1;
        $comments = $commentRepository->getPaginatedComments($page, $limit, $trick);
        $commentsCount = $trick->getComments()->count();
        $pages = (int)ceil($commentsCount / $limit);

        return $this->render('trick/show.html.twig', [ 
            'trick' => $trick,
            'comments' => $comments,
            'commentsCount' => $commentsCount,
            'page' => $page,
            'pages' => $pages,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Trick $trick
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route('/delete/{id}', name: 'app_trick_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_VERIFIED_USER', statusCode: 403, message: 'Vous devez être connecté et avoir une adresse email vérifiée pour accéder à cette page')]
    public function delete(Trick $trick, Request $request): Response
    {
        try {
            $this->denyAccessUnlessGranted('edit', $trick, 'Vous n\'avez pas l\'autorisation de supprimer ce trick');
        } catch (AccessDeniedException $e) {
            $this->addFlash('danger', $e->getMessage());
            return $this->redirectToRoute('app_home');
        }

        if ($this->isCsrfTokenValid('delete_' . $trick->getId(), $request->get('_token'))) {
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
     * @param Trick $trick
     * @param FormInterface $picturesForms
     * @return Trick
     */
    private function manageEditPicturesForms(Trick $trick, FormInterface $picturesForms): Trick
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
                        $this->removePictureFile($pictureForm->getData());
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
     * @param FormInterface $picturesForms
     * @return Trick
     */
    private function manageNewPicturesForms(Trick $trick, FormInterface $picturesForms): Trick
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
     * @param Trick $trick
     * @param FormInterface $videosForms
     * @return Trick
     */
    private function manageVideosForms(Trick $trick, FormInterface $videosForms): Trick
    {
        foreach ($videosForms as $videoForm) {
            // ignoring empty subforms
            if (null === $videoForm->getData()->getSource()) {
                $trick->removeVideo($videoForm->getData());
            }                
        }
        return $trick;
    }

    /**
     * @param Picture $picture
     * @return void
     */
    private function removePictureFile(Picture $picture): void
    {
        $oldPicture = $this->entityManager->getRepository(Picture::class)->findOneBy(['id' => $picture->getId()]);
        $this->fileManager->removeFile($oldPicture->getSource());
    }
}
