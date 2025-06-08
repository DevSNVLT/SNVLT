<?php

namespace App\Repository\References;

use App\Entity\References\PageDocGen;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PageDocGen>
 *
 * @method PageDocGen|null find($id, $lockMode = null, $lockVersion = null)
 * @method PageDocGen|null findOneBy(array $criteria, array $orderBy = null)
 * @method PageDocGen[]    findAll()
 * @method PageDocGen[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageDocGenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageDocGen::class);
    }

//    /**
//     * @return PageDocGen[] Returns an array of PageDocGen objects
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

//    public function findOneBySomeField($value): ?PageDocGen
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findLastPage($code_doc)
   {
        return $this->createQueryBuilder('p')
            ->select('max( p.seqPage) as dernier_numero')
            ->where('p.doctype = :code_doc')
            ->setParameter('code_doc', $code_doc)
            ->getQuery()
           ->getSingleScalarResult()
       ;
   }
}
