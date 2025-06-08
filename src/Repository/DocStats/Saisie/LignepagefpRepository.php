<?php

namespace App\Repository\DocStats\Saisie;

use App\Entity\DocStats\Saisie\Lignepagefp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lignepagefp>
 *
 * @method Lignepagefp|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lignepagefp|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lignepagefp[]    findAll()
 * @method Lignepagefp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LignepagefpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lignepagefp::class);
    }

//    /**
//     * @return Lignepagefp[] Returns an array of Lignepagefp objects
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

//    public function findOneBySomeField($value): ?Lignepagefp
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
