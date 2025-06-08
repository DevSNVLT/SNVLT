<?php

namespace App\Repository\DocStats\Entetes;

use App\Entity\DocStats\Entetes\Documentpdtdrv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Documentpdtdrv>
 *
 * @method Documentpdtdrv|null find($id, $lockMode = null, $lockVersion = null)
 * @method Documentpdtdrv|null findOneBy(array $criteria, array $orderBy = null)
 * @method Documentpdtdrv[]    findAll()
 * @method Documentpdtdrv[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentpdtdrvRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Documentpdtdrv::class);
    }

//    /**
//     * @return Documentpdtdrv[] Returns an array of Documentpdtdrv objects
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

//    public function findOneBySomeField($value): ?Documentpdtdrv
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
