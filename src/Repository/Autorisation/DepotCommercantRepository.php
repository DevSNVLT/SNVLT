<?php

namespace App\Repository\Autorisation;

use App\Entity\Autorisation\DepotCommercant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DepotCommercant>
 *
 * @method DepotCommercant|null find($id, $lockMode = null, $lockVersion = null)
 * @method DepotCommercant|null findOneBy(array $criteria, array $orderBy = null)
 * @method DepotCommercant[]    findAll()
 * @method DepotCommercant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepotCommercantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DepotCommercant::class);
    }

//    /**
//     * @return DepotCommercant[] Returns an array of DepotCommercant objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DepotCommercant
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
