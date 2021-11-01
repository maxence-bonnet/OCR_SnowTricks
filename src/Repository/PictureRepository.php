<?php

namespace App\Repository;

use App\Entity\Picture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Picture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Picture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Picture[]    findAll()
 * @method Picture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PictureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Picture::class);
    }


    // /**
    //  * @param Trick[] $tricks
    //  * @return ArrayCollection
    //  * Useless 
    //  */
    // public function findForTricks(array $tricks): ArrayCollection
    // {
    //     $pictures = $this->createQueryBuilder('p')
    //         ->select('p')
    //         ->where('p.trick IN (:tricks)')
    //         ->getQuery()
    //         ->setParameter('tricks', $tricks)
    //         ->getResult();
    //     // rebuilding ArrayCollection so that keys are Tricks Id, values are Pictures
    //     $pictures = array_reduce($pictures, function (array $accumulator, Picture $picture) {
    //         $accumulator[$picture->getTrick()->getId()] = $picture;
    //         return $accumulator;
    //     }, []);
    //     return new ArrayCollection($pictures);
    // }
}
