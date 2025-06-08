<?php

namespace App\Repository\Transformation;

use App\Entity\Transformation\Details2Transfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Details2Transfo>
 *
 * @method Details2Transfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Details2Transfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Details2Transfo[]    findAll()
 * @method Details2Transfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class Details2TransfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Details2Transfo::class);
    }

//    /**
//     * @return Details2Transfo[] Returns an array of Details2Transfo objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Details2Transfo
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
