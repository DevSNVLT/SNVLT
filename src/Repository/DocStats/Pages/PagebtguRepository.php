<?php

namespace App\Repository\DocStats\Pages;

use App\Entity\DocStats\Pages\Pagebtgu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pagebtgu>
 *
 * @method Pagebtgu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pagebtgu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pagebtgu[]    findAll()
 * @method Pagebtgu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PagebtguRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pagebtgu::class);
    }

//    /**
//     * @return Pagebtgu[] Returns an array of Pagebtgu objects
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

//    public function findOneBySomeField($value): ?Pagebtgu
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
