<?php

namespace App\Repository\DocStats\Pages;

use App\Entity\DocStats\Pages\Pagebcbp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pagebcbp>
 *
 * @method Pagebcbp|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pagebcbp|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pagebcbp[]    findAll()
 * @method Pagebcbp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PagebcbpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pagebcbp::class);
    }

//    /**
//     * @return Pagebcbp[] Returns an array of Pagebcbp objects
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

//    public function findOneBySomeField($value): ?Pagebcbp
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
