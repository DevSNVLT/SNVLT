<?php

namespace App\Repository\References;

use App\Entity\References\TypeForet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeForet>
 *
 * @method TypeForet|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeForet|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeForet[]    findAll()
 * @method TypeForet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeForetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeForet::class);
    }

//    /**
//     * @return TypeForet[] Returns an array of TypeForet objects
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

//    public function findOneBySomeField($value): ?TypeForet
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
