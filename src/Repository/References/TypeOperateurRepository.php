<?php

namespace App\Repository\References;

use App\Entity\References\TypeOperateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeOperateur>
 *
 * @method TypeOperateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeOperateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeOperateur[]    findAll()
 * @method TypeOperateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeOperateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeOperateur::class);
    }

//    /**
//     * @return TypeOperateur[] Returns an array of TypeOperateur objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TypeOperateur
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
