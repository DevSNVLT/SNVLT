<?php

namespace App\Repository\DocStats\Pages;

use App\Entity\DocStats\Pages\Pagepdtdrv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pagepdtdrv>
 *
 * @method Pagepdtdrv|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pagepdtdrv|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pagepdtdrv[]    findAll()
 * @method Pagepdtdrv[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PagepdtdrvRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pagepdtdrv::class);
    }

//    /**
//     * @return Pagepdtdrv[] Returns an array of Pagepdtdrv objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Pagepdtdrv
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
