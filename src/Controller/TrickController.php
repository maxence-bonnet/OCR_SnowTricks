<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Repository\TrickRepository;
use App\Form\TrickType;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class TrickController extends AbstractController
{
    /**
     * @var TrickRepository
     */
    private TrickRepository $trickRepository;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    public function __construct(TrickRepository $trickRepository, EntityManagerInterface $entityManager)
    {
        $this->trickRepository = $trickRepository;
        $this->entityManager = $entityManager;
    }

    /**
    * @param Trick $trick
    * @return \Symfony\Component\HttpFoundation\Response
    */
    #[Route('/trick/{id}/{slug}', name: 'app_trick_show', methods: ['GET','POST'])]
    public function show(Trick $trick, Request $request): Response
    {
        $comment = new Comment;

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comment
                ->setCreatedAt(new \DateTimeImmutable())
                ->setAuthor($this->getUser())
                ->setTrick($trick);

            $this->entityManager->persist($comment);

            $this->entityManager->flush();

            $this->addflash('success', 'Votre message a bien été ajouté');
        }        
     
        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'form' => $form->createView()
        ]);
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

            $trick->setCreatedAt(new \DateTimeImmutable());
            $trick->setAuthor($this->getUser());

            $this->entityManager->persist($trick);

            $this->entityManager->flush();

            $this->addflash('success', 'Le trick : ' . $trick->getTitle() . ' a bien été créé');

            return $this->redirectToRoute('app_trick_show', [
                'id' => $trick->getId(),
                'slug' => 'blabla' // @TODO : Ajouter le vrai slug
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

            $trick->setUpdatedAt(new \DateTimeImmutable());

            $this->entityManager->persist($trick);

            $this->entityManager->flush();

            $this->addflash('success', 'Le trick : ' . $trick->getTitle() . ' a bien été modifié');

            return $this->redirectToRoute('app_trick_show', [
                'id' => $trick->getId(),
                'slug' => 'blabla' // @TODO : Ajouter le vrai slug
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
    #[Route('/delete/{id}', name: 'app_trick_delete', methods: ['DELETE'])]
    public function delete(Trick $trick, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->get('_token'))) {
            $this->entityManager->remove($trick);
            $this->entityManager->flush();

            $this->addflash('success', 'Le trick : ' . $trick->getTitle() . ' a bien été supprimé');
        }
        return $this->redirectToRoute('app_home');
    }
}
