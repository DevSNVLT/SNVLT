<?php

namespace App\Repository\References;

use App\Entity\References\Dcg;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Dcg>
 *
 * @method Dcg|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dcg|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dcg[]    findAll()
 * @method Dcg[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DcgRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dcg::class);
    }

//    /**
//     * @return Dcg[] Returns an array of Dcg objects
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

//    public function findOneBySomeField($value): ?Dcg
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
