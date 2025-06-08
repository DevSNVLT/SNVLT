<?php

namespace App\Repository;

use App\Entity\Transformation\Fiche2Transfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Fiche2Transfo>
 *
 * @method Fiche2Transfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fiche2Transfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fiche2Transfo[]    findAll()
 * @method Fiche2Transfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class Fiche2TransfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fiche2Transfo::class);
    }

//    /**
//     * @return Fiche2Transfo[] Returns an array of Fiche2Transfo objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Fiche2Transfo
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
