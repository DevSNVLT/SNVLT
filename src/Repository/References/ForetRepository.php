<?php

namespace App\Repository\References;

use App\Entity\References\Foret;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Foret>
 *
 * @method Foret|null find($id, $lockMode = null, $lockVersion = null)
 * @method Foret|null findOneBy(array $criteria, array $orderBy = null)
 * @method Foret[]    findAll()
 * @method Foret[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ForetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Foret::class);
    }

//    /**
//     * @return Foret[] Returns an array of Foret objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Foret
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
