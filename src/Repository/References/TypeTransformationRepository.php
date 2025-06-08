<?php

namespace App\Repository\References;

use App\Entity\References\TypeTransformation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeTransformation>
 *
 * @method TypeTransformation|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeTransformation|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeTransformation[]    findAll()
 * @method TypeTransformation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeTransformationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeTransformation::class);
    }

//    /**
//     * @return TypeTransformation[] Returns an array of TypeTransformation objects
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

//    public function findOneBySomeField($value): ?TypeTransformation
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
