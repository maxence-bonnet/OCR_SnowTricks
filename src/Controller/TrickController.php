<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class TrickController extends AbstractController
{
    /**
     * @var TrickRepository
     */
    private TrickRepository $trickRepository;

    public function __construct(TrickRepository $trickRepository)
    {
        $this->trickRepository = $trickRepository;
    }

    #[Route('/trick/{id}/{slug}', name: 'app_trick_show')]
    public function show($id): Response
    {
        $trick = $this->trickRepository->findOneBy(['id' => $id]);

        return $this->render('trick/show.html.twig', [
            'trick' => $trick
        ]);
    }

    // #[Route('/trick/{id}/edit', name: 'app_trick_edit')]
    // public function edit(): Response
    // {

    //     return $this->render('trick/show.html.twig', [
    //         'trick' => $trick
    //     ]);
    // }

    // #[Route('/trick/new', name: 'app_trick_new')]
    // public function new(): Response
    // {
        
    //     return $this->render('trick/new.html.twig', [
    //     ]);
    // }

    // #[Route('/trick/{id}/delete', name: 'app_trick_delete', methods: ["DELETE"])]
    // public function delete(): Response
    // {
        
    //     return $this->render('trick/new.html.twig', [
    //     ]);
    // }
}
