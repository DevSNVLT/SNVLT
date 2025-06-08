<?php

namespace App\Repository;

use App\Entity\DisponibiliteParcBillons;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DisponibiliteParcBillons>
 *
 * @method DisponibiliteParcBillons|null find($id, $lockMode = null, $lockVersion = null)
 * @method DisponibiliteParcBillons|null findOneBy(array $criteria, array $orderBy = null)
 * @method DisponibiliteParcBillons[]    findAll()
 * @method DisponibiliteParcBillons[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DisponibiliteParcBillonsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DisponibiliteParcBillons::class);
    }

    /**
     * @return DisponibiliteParcBillons[] Returns an array of DisponibiliteParcBillons objects
     */
    public function findWithoutAucun(): array
   {
       return $this->createQueryBuilder('g')
           ->andWhere('g.id <> 0')
           ->andWhere('g.parent_DisponibiliteParcBillons = 0 or g.parent_DisponibiliteParcBillons is null')
            ->orderBy('g.nom_DisponibiliteParcBillons', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?DisponibiliteParcBillons
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
