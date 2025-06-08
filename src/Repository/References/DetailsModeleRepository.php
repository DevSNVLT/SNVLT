<?php

namespace App\Repository\References;

use App\Entity\References\DetailsModele;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DetailsModele>
 *
 * @method DetailsModele|null find($id, $lockMode = null, $lockVersion = null)
 * @method DetailsModele|null findOneBy(array $criteria, array $orderBy = null)
 * @method DetailsModele[]    findAll()
 * @method DetailsModele[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DetailsModeleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailsModele::class);
    }

//    /**
//     * @return DetailsModele[] Returns an array of DetailsModele objects
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

//    public function findOneBySomeField($value): ?DetailsModele
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
