<?php

namespace App\Repository\Autorisations;

use App\Entity\Autorisation\Reprise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reprise>
 *
 * @method Reprise|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reprise|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reprise[]    findAll()
 * @method Reprise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RepriseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reprise::class);
    }

//    /**
//     * @return Reprise[] Returns an array of Reprise objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Reprise
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
