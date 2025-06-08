<?php

namespace App\Repository\DocStats\Pages;

use App\Entity\DocStats\Pages\Pageetate2;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pageetate2>
 *
 * @method Pageetate2|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pageetate2|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pageetate2[]    findAll()
 * @method Pageetate2[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class Pageetate2Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pageetate2::class);
    }

//    /**
//     * @return Pageetate2[] Returns an array of Pageetate2 objects
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

//    public function findOneBySomeField($value): ?Pageetate2
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
