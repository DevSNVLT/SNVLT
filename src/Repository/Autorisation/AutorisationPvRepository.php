<?php

namespace App\Repository\Autorisation;

use App\Entity\Autorisation\AutorisationPv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AutorisationPv>
 *
 * @method AutorisationPv|null find($id, $lockMode = null, $lockVersion = null)
 * @method AutorisationPv|null findOneBy(array $criteria, array $orderBy = null)
 * @method AutorisationPv[]    findAll()
 * @method AutorisationPv[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AutorisationPvRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AutorisationPv::class);
    }

//    /**
//     * @return AutorisationPv[] Returns an array of AutorisationPv objects
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

//    public function findOneBySomeField($value): ?AutorisationPv
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
