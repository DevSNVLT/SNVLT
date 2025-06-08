<?php

namespace App\Repository\References;

use App\Entity\References\GrilleLegalite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GrilleLegalite>
 *
 * @method GrilleLegalite|null find($id, $lockMode = null, $lockVersion = null)
 * @method GrilleLegalite|null findOneBy(array $criteria, array $orderBy = null)
 * @method GrilleLegalite[]    findAll()
 * @method GrilleLegalite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GrilleLegaliteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GrilleLegalite::class);
    }

//    /**
//     * @return GrilleLegalite[] Returns an array of GrilleLegalite objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GrilleLegalite
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
