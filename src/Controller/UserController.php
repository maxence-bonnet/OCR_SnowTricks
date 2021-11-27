<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Picture;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\UserAvatarType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\FileManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends AbstractController
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
    #[Route('/myprofile', name: 'app_user_myprofile')]
    public function myProfile(Request $request, FileManager $fileManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserAvatarType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (null !== $form->get('file')->getData()) {
                // removing previous
                if ($user->getAvatar()) {
                    $fileManager->removeFile($user->getAvatar()->getSource(), 'avatar');
                    $this->entityManager->remove($user->getAvatar());
                }
                // uploading new
                $file = $form->get('file')->getData();
                /** @var UploadedFile $pictureFile */
                $newFilename = $fileManager->upload($file, 'avatar');
                $picture = (new Picture())
                    ->setSource($newFilename)
                    ->setAlternateText('avatar-' . $user->getUsername());
                $user->setAvatar($picture);
            }
 
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addflash('success', 'Le profil a bien été mis à jour');
        }

        // limiting n+1 queries
        $tricks = $this->getDoctrine()->getRepository(Trick::class)->findAllFromUserJoinPictures($user);
        $comments = $this->getDoctrine()->getRepository(Comment::class)->findLastsFromUser(10, $user);

        return $this->render('user/profile.html.twig', [
            'current_nav' => 'my_profile',
            'myProfile' => true,
            'user' => $user,
            'comments' => $comments,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route('/profile/{username}', name: 'app_user_profile')]
    public function profile(User $user): Response
    {
        // limiting n+1 queries
        $tricks = $this->getDoctrine()->getRepository(Trick::class)->findAllFromUserJoinPictures($user);
        $comments = $this->getDoctrine()->getRepository(Comment::class)->findLastsFromUser(10, $user);

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'comments' => $comments,
        ]);
    }
}