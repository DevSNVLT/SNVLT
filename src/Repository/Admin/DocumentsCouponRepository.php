<?php

namespace App\Repository\Admin;

use App\Entity\Admin\DocumentsCoupon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DocumentsCoupon>
 *
 * @method DocumentsCoupon|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentsCoupon|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentsCoupon[]    findAll()
 * @method DocumentsCoupon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentsCouponRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentsCoupon::class);
    }

//    /**
//     * @return DocumentsCoupon[] Returns an array of DocumentsCoupon objects
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

//    public function findOneBySomeField($value): ?DocumentsCoupon
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
