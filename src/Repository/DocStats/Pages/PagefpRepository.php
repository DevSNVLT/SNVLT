<?php

namespace App\Repository\DocStats\Pages;

use App\Entity\DocStats\Pages\Pagefp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pagefp>
 *
 * @method Pagefp|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pagefp|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pagefp[]    findAll()
 * @method Pagefp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PagefpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pagefp::class);
    }

//    /**
//     * @return Pagefp[] Returns an array of Pagefp objects
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

//    public function findOneBySomeField($value): ?Pagefp
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
