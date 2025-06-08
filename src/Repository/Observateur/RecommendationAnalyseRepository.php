<?php

namespace App\Repository\Observateur;

use App\Entity\Observateur\RecommendationAnalyse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RecommendationAnalyse>
 *
 * @method RecommendationAnalyse|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecommendationAnalyse|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecommendationAnalyse[]    findAll()
 * @method RecommendationAnalyse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecommendationAnalyseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecommendationAnalyse::class);
    }

//    /**
//     * @return RecommendationAnalyse[] Returns an array of RecommendationAnalyse objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RecommendationAnalyse
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
