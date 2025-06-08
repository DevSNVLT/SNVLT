<?php

namespace App\Repository\Transformation;

use App\Entity\Transformation\EltsProd;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EltsProd>
 *
 * @method EltsProd|null find($id, $lockMode = null, $lockVersion = null)
 * @method EltsProd|null findOneBy(array $criteria, array $orderBy = null)
 * @method EltsProd[]    findAll()
 * @method EltsProd[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EltsProdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EltsProd::class);
    }

//    /**
//     * @return EltsProd[] Returns an array of EltsProd objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EltsProd
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
