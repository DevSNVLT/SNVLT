<?php

namespace App\Repository\DocStats\Pages;

use App\Entity\DocStats\Pages\Pageetate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pageetate>
 *
 * @method Pageetate|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pageetate|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pageetate[]    findAll()
 * @method Pageetate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageetateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pageetate::class);
    }

//    /**
//     * @return Pageetate[] Returns an array of Pageetate objects
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

//    public function findOneBySomeField($value): ?Pageetate
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
