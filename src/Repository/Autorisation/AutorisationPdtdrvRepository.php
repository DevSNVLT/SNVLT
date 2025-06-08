<?php

namespace App\Repository\Autorisation;

use App\Entity\Autorisation\AutorisationPdtdrv;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AutorisationPdtdrv>
 *
 * @method AutorisationPdtdrv|null find($id, $lockMode = null, $lockVersion = null)
 * @method AutorisationPdtdrv|null findOneBy(array $criteria, array $orderBy = null)
 * @method AutorisationPdtdrv[]    findAll()
 * @method AutorisationPdtdrv[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AutorisationPdtdrvRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AutorisationPdtdrv::class);
    }

//    /**
//     * @return AutorisationPdtdrv[] Returns an array of AutorisationPdtdrv objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AutorisationPdtdrv
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
