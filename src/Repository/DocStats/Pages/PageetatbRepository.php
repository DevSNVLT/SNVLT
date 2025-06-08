<?php

namespace App\Repository\DocStats\Pages;

use App\Entity\DocStats\Pages\Pageetatb;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pageetatb>
 *
 * @method Pageetatb|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pageetatb|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pageetatb[]    findAll()
 * @method Pageetatb[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageetatbRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pageetatb::class);
    }

//    /**
//     * @return Pageetatb[] Returns an array of Pageetatb objects
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

//    public function findOneBySomeField($value): ?Pageetatb
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
