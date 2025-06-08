<?php

namespace App\Repository\Transformation;

use App\Entity\Transformation\FicheLotProd;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FicheLotProd>
 *
 * @method FicheLotProd|null find($id, $lockMode = null, $lockVersion = null)
 * @method FicheLotProd|null findOneBy(array $criteria, array $orderBy = null)
 * @method FicheLotProd[]    findAll()
 * @method FicheLotProd[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FicheLotProdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FicheLotProd::class);
    }

//    /**
//     * @return FicheLotProd[] Returns an array of FicheLotProd objects
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

//    public function findOneBySomeField($value): ?FicheLotProd
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
