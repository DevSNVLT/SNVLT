<?php

namespace App\Repository\Autorisations;

use App\Entity\Autorisation\Attribution;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Attribution>
 *
 * @method Attribution|null find($id, $lockMode = null, $lockVersion = null)
 * @method Attribution|null findOneBy(array $criteria, array $orderBy = null)
 * @method Attribution[]    findAll()
 * @method Attribution[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttributionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attribution::class);
    }

//    /**
//     * @return Autorisation[] Returns an array of Autorisation objects
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

//    public function findOneBySomeField($value): ?Autorisation
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
