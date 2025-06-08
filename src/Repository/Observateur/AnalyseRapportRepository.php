<?php

namespace App\Repository\Observateur;

use App\Entity\Observateur\AnalyseRapport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AnalyseRapport>
 *
 * @method AnalyseRapport|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnalyseRapport|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnalyseRapport[]    findAll()
 * @method AnalyseRapport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnalyseRapportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnalyseRapport::class);
    }

//    /**
//     * @return AnalyseRapport[] Returns an array of AnalyseRapport objects
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

//    public function findOneBySomeField($value): ?AnalyseRapport
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
