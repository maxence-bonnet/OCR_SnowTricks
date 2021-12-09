<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/admin/category')]
#[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Page réservée aux administrateurs')]
class AdminCategoryController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route('', name: 'app_admin_category')]
    public function index(CategoryRepository $repository, Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($category);
            $this->entityManager->flush();
            $this->addflash('success', 'La catégorie : ' . $category->getName() . ' a bien été créée!');
        }

        return $this->render('admin/category/index.html.twig', [
            'current_nav' => 'admin_categories',
            'categories' => $repository->findAllJoinTricks(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route('/delete/{id}', name: 'app_admin_category_delete', methods: ['DELETE'])]
    public function delete(Category $category, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete_' . $category->getId(), $request->get('_token'))) {
            $this->entityManager->remove($category);
            $this->entityManager->flush();
            $this->addflash('success', 'La catégorie : ' . $category->getName() . ' a bien été supprimée!');
        }
        return $this->redirectToRoute('app_admin_category');    
    }

}
