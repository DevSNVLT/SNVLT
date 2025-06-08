<?php

namespace App\Repository\Administration;

use App\Entity\Administration\FicheProspection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FicheProspection>
 *
 * @method FicheProspection|null find($id, $lockMode = null, $lockVersion = null)
 * @method FicheProspection|null findOneBy(array $criteria, array $orderBy = null)
 * @method FicheProspection[]    findAll()
 * @method FicheProspection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FicheProspectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FicheProspection::class);
    }

//    /**
//     * @return FicheProspection[] Returns an array of FicheProspection objects
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

//    public function findOneBySomeField($value): ?FicheProspection
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
