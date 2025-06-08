<?php

namespace App\Repository\DocStats\Pages;

use App\Entity\DocStats\Pages\Pageetatg;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pageetatg>
 *
 * @method Pageetatg|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pageetatg|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pageetatg[]    findAll()
 * @method Pageetatg[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageetatgRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pageetatg::class);
    }

//    /**
//     * @return Pageetatg[] Returns an array of Pageetatg objects
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

//    public function findOneBySomeField($value): ?Pageetatg
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
