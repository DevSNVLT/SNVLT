<?php

namespace App\Repository\Autorisation;

use App\Entity\Autorisation\AutorisationPs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AutorisationPs>
 *
 * @method AutorisationPs|null find($id, $lockMode = null, $lockVersion = null)
 * @method AutorisationPs|null findOneBy(array $criteria, array $orderBy = null)
 * @method AutorisationPs[]    findAll()
 * @method AutorisationPs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AutorisationPsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AutorisationPs::class);
    }

//    /**
//     * @return AutorisationPs[] Returns an array of AutorisationPs objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AutorisationPs
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
