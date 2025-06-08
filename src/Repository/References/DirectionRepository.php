<?php

namespace App\Repository\References;

use App\Entity\References\Direction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Direction>
 *
 * @method Direction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Direction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Direction[]    findAll()
 * @method Direction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DirectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Direction::class);
    }

    /**
     * @return Direction[] Returns an array of Direction objects
     */
    public function findWithResponsable(): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.email_personne_ressource is not null')
            ->orderBy('d.sigle', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?Direction
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
