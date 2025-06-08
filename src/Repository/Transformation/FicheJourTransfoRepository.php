<?php

namespace App\Repository\Transformation;

use App\Entity\Transformation\FicheJourTransfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FicheJourTransfo>
 *
 * @method FicheJourTransfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method FicheJourTransfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method FicheJourTransfo[]    findAll()
 * @method FicheJourTransfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FicheJourTransfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FicheJourTransfo::class);
    }

//    /**
//     * @return FicheJourTransfo[] Returns an array of FicheJourTransfo objects
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

//    public function findOneBySomeField($value): ?FicheJourTransfo
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
