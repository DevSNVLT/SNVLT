<?php

namespace App\Repository\Autorisations;

use App\Entity\Autorisation\AttributionPv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AttributionPv>
 *
 * @method AttributionPv|null find($id, $lockMode = null, $lockVersion = null)
 * @method AttributionPv|null findOneBy(array $criteria, array $orderBy = null)
 * @method AttributionPv[]    findAll()
 * @method AttributionPv[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttributionPvRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AttributionPv::class);
    }

//    /**
//     * @return AttributionPv[] Returns an array of AttributionPv objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AttributionPv
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
