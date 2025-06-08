<?php

namespace App\Repository\DocStats\Pages;

use App\Entity\DocStats\Pages\Pagebcbgfh;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pagebcbgfh>
 *
 * @method Pagebcbgfh|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pagebcbgfh|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pagebcbgfh[]    findAll()
 * @method Pagebcbgfh[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PagebcbgfhRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pagebcbgfh::class);
    }

//    /**
//     * @return Pagebcbgfh[] Returns an array of Pagebcbgfh objects
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

//    public function findOneBySomeField($value): ?Pagebcbgfh
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
