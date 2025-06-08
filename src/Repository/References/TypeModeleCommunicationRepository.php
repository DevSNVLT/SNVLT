<?php

namespace App\Repository\References;

use App\Entity\References\TypeModeleCommunication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeModeleCommunication>
 *
 * @method TypeModeleCommunication|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeModeleCommunication|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeModeleCommunication[]    findAll()
 * @method TypeModeleCommunication[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeModeleCommunicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeModeleCommunication::class);
    }

//    /**
//     * @return TypeModeleCommunication[] Returns an array of TypeModeleCommunication objects
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

//    public function findOneBySomeField($value): ?TypeModeleCommunication
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
