<?php

namespace App\Repository;

use App\Entity\References\TypeAutorisation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeAutorisation>
 *
 * @method TypeAutorisation|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeAutorisation|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeAutorisation[]    findAll()
 * @method TypeAutorisation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeAutorisationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeAutorisation::class);
    }

//    /**
//     * @return TypeAutorisation[] Returns an array of TypeAutorisation objects
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

//    public function findOneBySomeField($value): ?TypeAutorisation
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
