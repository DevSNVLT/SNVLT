<?php

namespace App\Repository;

use App\Entity\DisponibiliteParcBilles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DisponibiliteParcBilles>
 *
 * @method DisponibiliteParcBilles|null find($id, $lockMode = null, $lockVersion = null)
 * @method DisponibiliteParcBilles|null findOneBy(array $criteria, array $orderBy = null)
 * @method DisponibiliteParcBilles[]    findAll()
 * @method DisponibiliteParcBilles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DisponibiliteParcBillesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DisponibiliteParcBilles::class);
    }

    /**
     * @return DisponibiliteParcBilles[] Returns an array of DisponibiliteParcBilles objects
     */
    public function findWithoutAucun(): array
   {
       return $this->createQueryBuilder('g')
           ->andWhere('g.id <> 0')
           ->andWhere('g.parent_DisponibiliteParcBilles = 0 or g.parent_DisponibiliteParcBilles is null')
            ->orderBy('g.nom_DisponibiliteParcBilles', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?DisponibiliteParcBilles
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
