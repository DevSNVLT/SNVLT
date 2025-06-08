<?php

namespace App\Repository\Requetes;

use App\Entity\Requetes\MenuRequetes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MenuRequetes>
 *
 * @method MenuRequetes|null find($id, $lockMode = null, $lockVersion = null)
 * @method MenuRequetes|null findOneBy(array $criteria, array $orderBy = null)
 * @method MenuRequetes[]    findAll()
 * @method MenuRequetes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuRequetesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenuRequetes::class);
    }

//    /**
//     * @return MenuRequetes[] Returns an array of MenuRequetes objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MenuRequetes
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
