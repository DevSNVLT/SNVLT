<?php

namespace App\Repository\Transformation;

use App\Entity\Transformation\ElementsColis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ElementsColis>
 *
 * @method ElementsColis|null find($id, $lockMode = null, $lockVersion = null)
 * @method ElementsColis|null findOneBy(array $criteria, array $orderBy = null)
 * @method ElementsColis[]    findAll()
 * @method ElementsColis[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ElementsColisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ElementsColis::class);
    }

//    /**
//     * @return ElementsColis[] Returns an array of ElementsColis objects
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

//    public function findOneBySomeField($value): ?ElementsColis
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
