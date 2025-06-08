<?php

namespace App\Repository\DocStats\Saisie;

use App\Entity\DocStats\Saisie\Lignepagecp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lignepagecp>
 *
 * @method Lignepagecp|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lignepagecp|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lignepagecp[]    findAll()
 * @method Lignepagecp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LignepagecpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lignepagecp::class);
    }

//    /**
//     * @return Lignepagecp[] Returns an array of Lignepagecp objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Lignepagecp
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
