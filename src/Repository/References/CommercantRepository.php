<?php

namespace App\Repository\References;

use App\Entity\References\Commercant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commercant>
 *
 * @method Commercant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commercant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commercant[]    findAll()
 * @method Commercant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommercantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commercant::class);
    }

//    /**
//     * @return Commercant[] Returns an array of Commercant objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Commercant
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
