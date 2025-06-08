<?php

namespace App\Repository\DocStats\Pages;

use App\Entity\DocStats\Pages\Pagedmv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pagedmv>
 *
 * @method Pagedmv|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pagedmv|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pagedmv[]    findAll()
 * @method Pagedmv[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PagedmvRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pagedmv::class);
    }

//    /**
//     * @return Pagedmv[] Returns an array of Pagedmv objects
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

//    public function findOneBySomeField($value): ?Pagedmv
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
