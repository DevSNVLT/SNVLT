<?php

namespace App\Repository\References;

use App\Entity\References\AttributairePs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AttributairePs>
 *
 * @method AttributairePs|null find($id, $lockMode = null, $lockVersion = null)
 * @method AttributairePs|null findOneBy(array $criteria, array $orderBy = null)
 * @method AttributairePs[]    findAll()
 * @method AttributairePs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttributairePsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AttributairePs::class);
    }

//    /**
//     * @return AttributairePs[] Returns an array of AttributairePs objects
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

//    public function findOneBySomeField($value): ?AttributairePs
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
