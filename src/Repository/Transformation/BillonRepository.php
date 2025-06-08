<?php

namespace App\Repository\Transformation;

use App\Entity\Transformation\Billon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Billon>
 *
 * @method Billon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Billon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Billon[]    findAll()
 * @method Billon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BillonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Billon::class);
    }

    /**
    * @return Billon[] Returns an array of Billon objects
     */
    public function findByExampleField($value): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
   }

//    public function findOneBySomeField($value): ?Billon
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
