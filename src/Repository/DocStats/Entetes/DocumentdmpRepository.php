<?php

namespace App\Repository\DocStats\Entetes;

use App\Entity\DocStats\Entetes\Documentdmp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Documentdmp>
 *
 * @method Documentdmp|null find($id, $lockMode = null, $lockVersion = null)
 * @method Documentdmp|null findOneBy(array $criteria, array $orderBy = null)
 * @method Documentdmp[]    findAll()
 * @method Documentdmp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentdmpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Documentdmp::class);
    }

//    /**
//     * @return Documentdmp[] Returns an array of Documentdmp objects
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

//    public function findOneBySomeField($value): ?Documentdmp
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
