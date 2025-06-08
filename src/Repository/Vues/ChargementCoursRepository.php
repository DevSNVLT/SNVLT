<?php

namespace App\Repository\Vues;

use App\Entity\Vues\ChargementCours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChargementCours>
 *
 * @method ChargementCours|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChargementCours|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChargementCours[]    findAll()
 * @method ChargementCours[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChargementCoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChargementCours::class);
    }

//    /**
//     * @return Fiche2Transfo[] Returns an array of Fiche2Transfo objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Fiche2Transfo
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
