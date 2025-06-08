<?php

namespace App\Repository;

use App\Entity\Administration\DemandeOperateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DemandeOperateur>
 *
 * @method DemandeOperateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method DemandeOperateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method DemandeOperateur[]    findAll()
 * @method DemandeOperateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdministrationDemandeOperateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandeOperateur::class);
    }

//    /**
//     * @return AdministrationDemandeOperateur[] Returns an array of AdministrationDemandeOperateur objects
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

//    public function findOneBySomeField($value): ?AdministrationDemandeOperateur
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
