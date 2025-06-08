<?php

namespace App\Repository\Transformation;

use App\Entity\Transformation\FicheLot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FicheLot>
 *
 * @method FicheLot|null find($id, $lockMode = null, $lockVersion = null)
 * @method FicheLot|null findOneBy(array $criteria, array $orderBy = null)
 * @method FicheLot[]    findAll()
 * @method FicheLot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FicheLotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FicheLot::class);
    }

//    /**
//     * @return FicheLot[] Returns an array of FicheLot objects
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

//    public function findOneBySomeField($value): ?FicheLot
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
