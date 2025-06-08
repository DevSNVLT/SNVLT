<?php

namespace App\Repository\DocStats\Pages;

use App\Entity\DocStats\Pages\Pagedmp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pagedmp>
 *
 * @method Pagedmp|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pagedmp|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pagedmp[]    findAll()
 * @method Pagedmp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PagedmpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pagedmp::class);
    }

//    /**
//     * @return Pagedmp[] Returns an array of Pagedmp objects
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

//    public function findOneBySomeField($value): ?Pagedmp
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
