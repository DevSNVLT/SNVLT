<?php

namespace App\Repository\References;

use App\Entity\References\Oi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Oi>
 *
 * @method Oi|null find($id, $lockMode = null, $lockVersion = null)
 * @method Oi|null findOneBy(array $criteria, array $orderBy = null)
 * @method Oi[]    findAll()
 * @method Oi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Oi::class);
    }

//    /**
//     * @return Oi[] Returns an array of Oi objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Oi
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
