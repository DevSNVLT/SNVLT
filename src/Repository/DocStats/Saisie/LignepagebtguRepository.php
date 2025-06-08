<?php

namespace App\Repository\DocStats\Saisie;

use App\Entity\DocStats\Saisie\Lignepagebtgu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lignepagebtgu>
 *
 * @method Lignepagebtgu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lignepagebtgu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lignepagebtgu[]    findAll()
 * @method Lignepagebtgu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LignepagebtguRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lignepagebtgu::class);
    }

//    /**
//     * @return Lignepagebtgu[] Returns an array of Lignepagebtgu objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Lignepagebtgu
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
