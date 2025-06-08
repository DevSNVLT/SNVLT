<?php

namespace App\Repository\DocStats\Pages;

use App\Entity\DocStats\Pages\Pagelje;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pagelje>
 *
 * @method Pagelje|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pagelje|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pagelje[]    findAll()
 * @method Pagelje[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageljeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pagelje::class);
    }

//    /**
//     * @return Pagelje[] Returns an array of Pagelje objects
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

//    public function findOneBySomeField($value): ?Pagelje
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
