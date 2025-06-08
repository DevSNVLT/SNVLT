<?php

namespace App\Repository\Observateur;

use App\Entity\Observateur\TypeRapport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeRapport>
 *
 * @method TypeRapport|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeRapport|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeRapport[]    findAll()
 * @method TypeRapport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeRapportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeRapport::class);
    }

//    /**
//     * @return TypeRapport[] Returns an array of TypeRapport objects
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

//    public function findOneBySomeField($value): ?TypeRapport
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
