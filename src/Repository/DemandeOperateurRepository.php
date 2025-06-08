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
class DemandeOperateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandeOperateur::class);
    }

//    /**
//     * @return DemandeOperateur[] Returns an array of DemandeOperateur objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DemandeOperateur
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
