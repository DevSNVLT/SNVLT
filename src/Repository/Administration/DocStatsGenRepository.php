<?php

namespace App\Repository\Administration;

use App\Entity\Administration\DocStatsGen;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DocStatsGen>
 *
 * @method DocStatsGen|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocStatsGen|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocStatsGen[]    findAll()
 * @method DocStatsGen[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocStatsGenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocStatsGen::class);
    }

    /**
     * @return DocStatsGen[] Returns an array of DocStatsGen objects
     */
    public function shwoDocumentsGenDetails($code_stock): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.code_type_doc = :val')
            ->setParameter('val', $code_stock)
           ->orderBy('d.id', 'ASC')
            ->getQuery()
           ->getResult()
       ;
   }

    public function findLastDocNumber($code_doc)
    {
        return $this->createQueryBuilder('d')
            ->select('max( d.numdoc) as dernier_numero')
            ->where('d.docname = :code_doc')
            ->setParameter('code_doc', $code_doc)
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    public function stockRestant($code_type_doc)
    {
        return $this->createQueryBuilder('d')
            ->select('count(d.id) as nb')
            ->where('d.attribue = false')
            ->andWhere('d.docname = :val')
            ->setParameter('val', $code_type_doc)
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }
//    public function findOneBySomeField($value): ?DocStatsGen
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
