<?php

namespace App\Repository\DocStats\Entetes;

use App\Entity\DocStats\Entetes\Documentetatg;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Documentetatg>
 *
 * @method Documentetatg|null find($id, $lockMode = null, $lockVersion = null)
 * @method Documentetatg|null findOneBy(array $criteria, array $orderBy = null)
 * @method Documentetatg[]    findAll()
 * @method Documentetatg[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentetatgRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Documentetatg::class);
    }

//    /**
//     * @return Documentetatg[] Returns an array of Documentetatg objects
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

//    public function findOneBySomeField($value): ?Documentetatg
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
