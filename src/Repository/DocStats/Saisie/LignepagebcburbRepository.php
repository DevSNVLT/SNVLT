<?php

namespace App\Repository\DocStats\Saisie;

use App\Entity\DocStats\Saisie\Lignepagebcburb;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lignepagebcburb>
 *
 * @method Lignepagebcburb|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lignepagebcburb|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lignepagebcburb[]    findAll()
 * @method Lignepagebcburb[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LignepagebcburbRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lignepagebcburb::class);
    }

//    /**
//     * @return Lignepagebcburb[] Returns an array of Lignepagebcburb objects
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

//    public function findOneBySomeField($value): ?Lignepagebcburb
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
