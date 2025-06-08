<?php

namespace App\Repository\Observateur;

use App\Entity\Observateur\Fichiers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Fichiers>
 *
 * @method Fichiers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fichiers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fichiers[]    findAll()
 * @method Fichiers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FichiersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fichiers::class);
    }

//    /**
//     * @return Fichiers[] Returns an array of Fichiers objects
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

//    public function findOneBySomeField($value): ?Fichiers
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
