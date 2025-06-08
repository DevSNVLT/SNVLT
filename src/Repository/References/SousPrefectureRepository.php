<?php

namespace App\Repository\References;

use App\Entity\References\SousPrefecture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SousPrefecture>
 *
 * @method SousPrefecture|null find($id, $lockMode = null, $lockVersion = null)
 * @method SousPrefecture|null findOneBy(array $criteria, array $orderBy = null)
 * @method SousPrefecture[]    findAll()
 * @method SousPrefecture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SousPrefectureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SousPrefecture::class);
    }

//    /**
//     * @return SousPrefecture[] Returns an array of SousPrefecture objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SousPrefecture
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
