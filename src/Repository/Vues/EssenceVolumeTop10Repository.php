<?php

namespace App\Repository\Vues;

use App\Entity\Vues\EssenceVolumeTop10;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EssenceVolumeTop10>
 *
 * @method EssenceVolumeTop10|null find($id, $lockMode = null, $lockVersion = null)
 * @method EssenceVolumeTop10|null findOneBy(array $criteria, array $orderBy = null)
 * @method EssenceVolumeTop10[]    findAll()
 * @method EssenceVolumeTop10[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EssenceVolumeTop10Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EssenceVolumeTop10::class);
    }

//    /**
//     * @return Fiche2Transfo[] Returns an array of Fiche2Transfo objects
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

//    public function findOneBySomeField($value): ?Fiche2Transfo
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
