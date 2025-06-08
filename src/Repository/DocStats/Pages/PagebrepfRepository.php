<?php

namespace App\Repository\DocStats\Pages;

use App\Entity\DocStats\Pages\Pagebrepf;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pagebrepf>
 *
 * @method Pagebrepf|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pagebrepf|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pagebrepf[]    findAll()
 * @method Pagebrepf[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PagebrepfRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pagebrepf::class);
    }

//    /**
//     * @return Pagebrepf[] Returns an array of Pagebrepf objects
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

//    public function findOneBySomeField($value): ?Pagebrepf
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
