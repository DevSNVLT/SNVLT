<?php

namespace App\Repository\DocStats\Saisie;

use App\Entity\DocStats\Saisie\Lignepageetate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lignepageetate>
 *
 * @method Lignepageetate|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lignepageetate|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lignepageetate[]    findAll()
 * @method Lignepageetate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LignepageetateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lignepageetate::class);
    }

//    /**
//     * @return Lignepageetate[] Returns an array of Lignepageetate objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Lignepageetate
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
