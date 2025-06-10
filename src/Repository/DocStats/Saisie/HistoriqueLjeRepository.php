<?php

namespace App\Repository\DocStats\Saisie;

use App\Entity\DocStats\Saisie\HistoriqueLje;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HistoriqueLje>
 *
 * @method HistoriqueLje|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoriqueLje|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoriqueLje[]    findAll()
 * @method HistoriqueLje[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoriqueLjeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistoriqueLje::class);
    }

//    /**
//     * @return HistoriqueLje[] Returns an array of HistoriqueLje objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?HistoriqueLje
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
