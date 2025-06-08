<?php

namespace App\Repository\DocStats\Entetes;

use App\Entity\DocStats\Entetes\Documentcp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Documentcp>
 *
 * @method Documentcp|null find($id, $lockMode = null, $lockVersion = null)
 * @method Documentcp|null findOneBy(array $criteria, array $orderBy = null)
 * @method Documentcp[]    findAll()
 * @method Documentcp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentcpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Documentcp::class);
    }

//    /**
//     * @return Documentcp[] Returns an array of Documentcp objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Documentcp
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
