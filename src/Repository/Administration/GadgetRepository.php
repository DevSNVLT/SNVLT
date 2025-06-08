<?php

namespace App\Repository\Administration;

use App\Entity\Administration\Gadget;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Gadget>
 *
 * @method Gadget|null find($id, $lockMode = null, $lockVersion = null)
 * @method Gadget|null findOneBy(array $criteria, array $orderBy = null)
 * @method Gadget[]    findAll()
 * @method Gadget[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GadgetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gadget::class);
    }

//    /**
//     * @return Gadget[] Returns an array of Gadget objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Gadget
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
