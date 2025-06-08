<?php

namespace App\Repository\References;

use App\Entity\References\Essence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Essence>
 *
 * @method Essence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Essence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Essence[]    findAll()
 * @method Essence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EssenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Essence::class);
    }

//    /**
//     * @return Essence[] Returns an array of Essence objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Essence
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
