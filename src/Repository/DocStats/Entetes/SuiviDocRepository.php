<?php

namespace App\Repository\DocStats\Entetes;

use App\Entity\DocStats\Entetes\SuiviDoc;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SuiviDoc>
 *
 * @method SuiviDoc|null find($id, $lockMode = null, $lockVersion = null)
 * @method SuiviDoc|null findOneBy(array $criteria, array $orderBy = null)
 * @method SuiviDoc[]    findAll()
 * @method SuiviDoc[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SuiviDocRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SuiviDoc::class);
    }

//    /**
//     * @return SuiviDoc[] Returns an array of SuiviDoc objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SuiviDoc
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
