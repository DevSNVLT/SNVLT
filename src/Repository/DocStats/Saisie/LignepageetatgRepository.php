<?php

namespace App\Repository\DocStats\Saisie;

use App\Entity\DocStats\Saisie\Lignepageetatg;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lignepageetatg>
 *
 * @method Lignepageetatg|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lignepageetatg|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lignepageetatg[]    findAll()
 * @method Lignepageetatg[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LignepageetatgRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lignepageetatg::class);
    }

//    /**
//     * @return Lignepageetatg[] Returns an array of Lignepageetatg objects
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

//    public function findOneBySomeField($value): ?Lignepageetatg
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
