<?php

namespace App\Repository\DocStats\Pages;

use App\Entity\DocStats\Pages\Pagebth;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pagebth>
 *
 * @method Pagebth|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pagebth|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pagebth[]    findAll()
 * @method Pagebth[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PagebthRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pagebth::class);
    }

//    /**
//     * @return Pagebth[] Returns an array of Pagebth objects
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

//    public function findOneBySomeField($value): ?Pagebth
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
