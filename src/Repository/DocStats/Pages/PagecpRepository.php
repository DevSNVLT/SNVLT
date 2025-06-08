<?php

namespace App\Repository\DocStats\Pages;

use App\Entity\DocStats\Pages\Pagecp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pagecp>
 *
 * @method Pagecp|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pagecp|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pagecp[]    findAll()
 * @method Pagecp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PagecpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pagecp::class);
    }

//    /**
//     * @return Pagecp[] Returns an array of Pagecp objects
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

//    public function findOneBySomeField($value): ?Pagecp
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
