<?php

namespace App\Repository\DocStats\Saisie;

use App\Entity\DocStats\Saisie\Lignepageetath;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lignepageetath>
 *
 * @method Lignepageetath|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lignepageetath|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lignepageetath[]    findAll()
 * @method Lignepageetath[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LignepageetathRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lignepageetath::class);
    }

//    /**
//     * @return Lignepageetath[] Returns an array of Lignepageetath objects
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

//    public function findOneBySomeField($value): ?Lignepageetath
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
