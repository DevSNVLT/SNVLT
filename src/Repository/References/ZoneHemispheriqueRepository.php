<?php

namespace App\Repository\References;

use App\Entity\References\ZoneHemispherique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ZoneHemispherique>
 *
 * @method ZoneHemispherique|null find($id, $lockMode = null, $lockVersion = null)
 * @method ZoneHemispherique|null findOneBy(array $criteria, array $orderBy = null)
 * @method ZoneHemispherique[]    findAll()
 * @method ZoneHemispherique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ZoneHemispheriqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ZoneHemispherique::class);
    }

//    /**
//     * @return ZoneHemispherique[] Returns an array of ZoneHemispherique objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('z')
//            ->andWhere('z.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('z.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ZoneHemispherique
//    {
//        return $this->createQueryBuilder('z')
//            ->andWhere('z.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
