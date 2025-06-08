<?php

namespace App\Repository\DocStats\Saisie;

use App\Entity\DocStats\Saisie\Lignepageetate2;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lignepageetate2>
 *
 * @method Lignepageetate2|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lignepageetate2|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lignepageetate2[]    findAll()
 * @method Lignepageetate2[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class Lignepageetate2Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lignepageetate2::class);
    }

//    /**
//     * @return Lignepageetate2[] Returns an array of Lignepageetate2 objects
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

//    public function findOneBySomeField($value): ?Lignepageetate2
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
