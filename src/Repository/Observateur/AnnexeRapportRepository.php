<?php

namespace App\Repository\Observateur;

use App\Entity\Observateur\AnnexeRapport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AnnexeRapport>
 *
 * @method AnnexeRapport|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnnexeRapport|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnnexeRapport[]    findAll()
 * @method AnnexeRapport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnexeRapportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnnexeRapport::class);
    }

//    /**
//     * @return AnnexeRapport[] Returns an array of AnnexeRapport objects
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

//    public function findOneBySomeField($value): ?AnnexeRapport
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
