<?php

namespace App\Repository;

use App\Entity\Trick;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Trick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trick[]    findAll()
 * @method Trick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trick::class);
    }

    /**
     * @return Trick[] Returns an array of Trick objects
     */
    public function findAllJoinAll()
    {
        $tricks = $this->createQueryBuilder('t')
            ->select('t', 'p', 'c', 'u')
            ->leftJoin('t.pictures', 'p')
            ->leftJoin('t.category', 'c')
            ->leftjoin('t.usersWhiteList', 'u')
            ->getQuery()
            ->getResult();

        return $tricks;
    }

    /**
     * @return Trick[] Returns an array of Trick objects
     * @param User $user
     */
    public function findAllJoinAllFromUser(User $user)
    {
        $tricks = $this->createQueryBuilder('t')
            ->select('t', 'p', 'c', 'u')
            ->where('t.author = :user')
            ->setParameter('user', $user)
            ->leftJoin('t.pictures', 'p')
            ->leftJoin('t.category', 'c')
            ->leftjoin('t.usersWhiteList', 'u')
            ->getQuery()
            ->getResult();

        return $tricks;
    }
    
    /**
     * @return Trick[] Returns an array of Trick objects
     * @param User $user
     */
    public function findAllAllowedTricksFromUser(User $user)
    {
        $tricks = $this->createQueryBuilder('t')
            ->select('t', 'p', 'c', 'u')
            ->where('t.author = :user')
            ->setParameter('user', $user)
            ->orWhere(':user MEMBER OF t.usersWhiteList')
            ->setParameter('user', $user)
            ->leftJoin('t.category', 'c')
            ->leftJoin('t.pictures', 'p')
            ->leftjoin('t.usersWhiteList', 'u')
            ->getQuery()
            ->getResult();

        return $tricks;
    }
}
