<?php

namespace App\Repository\Transformation;

use App\Entity\Transformation\DetailFicheJourTransfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DetailFicheJourTransfo>
 *
 * @method DetailFicheJourTransfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method DetailFicheJourTransfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method DetailFicheJourTransfo[]    findAll()
 * @method DetailFicheJourTransfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DetailFicheJourTransfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailFicheJourTransfo::class);
    }

//    /**
//     * @return DetailFicheJourTransfo[] Returns an array of DetailFicheJourTransfo objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DetailFicheJourTransfo
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
