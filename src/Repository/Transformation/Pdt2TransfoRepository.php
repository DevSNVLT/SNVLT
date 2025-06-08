<?php

namespace App\Repository\Transformation;

use App\Entity\Transformation\Pdt2Transfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pdt2Transfo>
 *
 * @method Pdt2Transfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pdt2Transfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pdt2Transfo[]    findAll()
 * @method Pdt2Transfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class Pdt2TransfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pdt2Transfo::class);
    }

//    /**
//     * @return Pdt2Transfo[] Returns an array of Pdt2Transfo objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Pdt2Transfo
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
