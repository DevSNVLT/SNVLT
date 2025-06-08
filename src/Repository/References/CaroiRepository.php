<?php

namespace App\Repository\References;

use App\Entity\References\Caroi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Caroi>
 *
 * @method Caroi|null find($id, $lockMode = null, $lockVersion = null)
 * @method Caroi|null findOneBy(array $criteria, array $orderBy = null)
 * @method Caroi[]    findAll()
 * @method Caroi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CaroiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Caroi::class);
    }

//    /**
//     * @return Caroi[] Returns an array of Caroi objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Caroi
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
