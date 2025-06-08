<?php

namespace App\Repository\Administration;

use App\Entity\Administration\StockDoc;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StockDoc>
 *
 * @method StockDoc|null find($id, $lockMode = null, $lockVersion = null)
 * @method StockDoc|null findOneBy(array $criteria, array $orderBy = null)
 * @method StockDoc[]    findAll()
 * @method StockDoc[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockDocRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockDoc::class);
    }

/*
 * re
 */
    public function findSoldeByDocument($value)
    {
       return $this->createQueryBuilder('s')
           ->select('sum(s.qte) as qte')
           /*->groupBy('s.code_type_doc_stat')*/
           ->andWhere('s.code_type_doc_stat = :val')
            ->setParameter('val', $value)
            ->getQuery()
           ->getSingleScalarResult()
        ;
    }

//    public function findOneBySomeField($value): ?StockDoc
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
