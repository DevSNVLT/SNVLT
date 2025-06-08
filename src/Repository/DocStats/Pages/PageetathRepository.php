<?php

namespace App\Repository\DocStats\Pages;

use App\Entity\DocStats\Pages\Pageetath;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pageetath>
 *
 * @method Pageetath|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pageetath|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pageetath[]    findAll()
 * @method Pageetath[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageetathRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pageetath::class);
    }

//    /**
//     * @return Pageetath[] Returns an array of Pageetath objects
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

//    public function findOneBySomeField($value): ?Pageetath
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
