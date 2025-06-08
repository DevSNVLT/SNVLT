<?php

namespace App\Repository\DocStats\Entetes;

use App\Entity\DocStats\Entetes\Documentbtgu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Documentbtgu>
 *
 * @method Documentbtgu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Documentbtgu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Documentbtgu[]    findAll()
 * @method Documentbtgu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentbtguRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Documentbtgu::class);
    }

//    /**
//     * @return Documentbtgu[] Returns an array of Documentbtgu objects
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

//    public function findOneBySomeField($value): ?Documentbtgu
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
