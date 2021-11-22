<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @param int $page
     * @param int $limit
     * @param Trick $trick
     * @return Comment[]
     */
    public function getPaginatedComments(int $page, int $limit, Trick $trick)
    {
        $comments = $this->createQueryBuilder('c')
            ->where('c.trick = :trick')
            ->setParameter('trick', $trick)
            ->orderBy('c.createdAt', 'DESC')
            ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
        return $comments;
    }

    /**
     * @param int $number number of comments requested
     * @param User $user
     * @return Comment[]
     */
    public function findLastsFromUser(int $number, User $user)
    {
        $comments = $this->createQueryBuilder('c')
            ->where('c.author = :user')
            ->setParameter('user', $user)
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults($number)
            ->getQuery()
            ->getResult()
        ;
        return $comments;
    }
}
