<?php

namespace App\Repository\Requetes;

use App\Entity\Requetes\TypeRequetes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeRequetes>
 *
 * @method TypeRequetes|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeRequetes|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeRequetes[]    findAll()
 * @method TypeRequetes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeRequetesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeRequetes::class);
    }

//    /**
//     * @return TypeRequetes[] Returns an array of TypeRequetes objects
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

//    public function findOneBySomeField($value): ?TypeRequetes
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
