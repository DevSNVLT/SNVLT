<?php

namespace App\Repository\DocStats\Pages;

use App\Entity\DocStats\Pages\Pagebcburb;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pagebcburb>
 *
 * @method Pagebcburb|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pagebcburb|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pagebcburb[]    findAll()
 * @method Pagebcburb[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PagebcburbRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pagebcburb::class);
    }

//    /**
//     * @return Pagebcburb[] Returns an array of Pagebcburb objects
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

//    public function findOneBySomeField($value): ?Pagebcburb
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
