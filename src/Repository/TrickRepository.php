<?php

namespace App\Repository;

use App\Entity\Trick;
use App\Entity\Picture;
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

    // /**
    //  * @return Trick[] Returns an array of Trick objects
    //  * Useless
    //  */
    // public function findAllWithFirstPicture()
    // {
    //     $tricks = $this->createQueryBuilder('t')
    //         ->getQuery()
    //         ->getResult();

    //     $pictures = $this->getEntityManager()->getRepository(Picture::class)->findForTricks($tricks);

    //     foreach ($tricks as $trick) {
    //         if ($pictures->containsKey($trick->getId())) {
    //             $trick->addPicture($pictures->get($trick->getId()));
    //         }
    //     }

    //     return $tricks;
    // }

    /**
     * @return Trick[] Returns an array of Trick objects
     * Useless
     */
    public function findAllJoinPictures()
    {
        $tricks = $this->createQueryBuilder('t')
            ->select('t', 'p')
            ->leftJoin('t.pictures', 'p')
            ->getQuery()
            ->getResult();

        return $tricks;
    }

}
