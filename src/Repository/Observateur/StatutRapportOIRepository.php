<?php

namespace App\Repository\Observateur;

use App\Entity\Observateur\StatutRapportOI;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StatutRapportOI>
 *
 * @method StatutRapportOI|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatutRapportOI|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatutRapportOI[]    findAll()
 * @method StatutRapportOI[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatutRapportOIRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatutRapportOI::class);
    }

//    /**
//     * @return StatutRapportOI[] Returns an array of StatutRapportOI objects
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

//    public function findOneBySomeField($value): ?StatutRapportOI
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
