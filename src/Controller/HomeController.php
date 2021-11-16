<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route('/', name: 'app_home')]
    public function home(TrickRepository $repository): Response
    {
        $displayParameters = [
            'minTricksShown' => 10,
            'maxToggleSteps' => 5,
            'defaultStepTimer' => 300 // (ms)
        ];

        $tricks = $repository->findAllJoinPictures();
        
        return $this->render('home/index.html.twig', [
            'current_nav' => 'home',
            'tricks' => $tricks,
            'displayParameters' => $displayParameters
        ]);
    }
}
