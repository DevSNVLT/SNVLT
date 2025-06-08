<?php

namespace App\Repository\DocStats\Saisie;

use App\Entity\DocStats\Saisie\Lignepagepdtdrv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lignepagepdtdrv>
 *
 * @method Lignepagepdtdrv|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lignepagepdtdrv|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lignepagepdtdrv[]    findAll()
 * @method Lignepagepdtdrv[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LignepagepdtdrvRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lignepagepdtdrv::class);
    }

//    /**
//     * @return Lignepagepdtdrv[] Returns an array of Lignepagepdtdrv objects
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

//    public function findOneBySomeField($value): ?Lignepagepdtdrv
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
