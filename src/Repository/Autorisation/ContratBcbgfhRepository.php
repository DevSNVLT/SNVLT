<?php

namespace App\Repository\Autorisation;

use App\Entity\Autorisation\ContratBcbgfh;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContratBcbgfh>
 *
 * @method ContratBcbgfh|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContratBcbgfh|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContratBcbgfh[]    findAll()
 * @method ContratBcbgfh[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContratBcbgfhRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContratBcbgfh::class);
    }

//    /**
//     * @return ContratBcbgfh[] Returns an array of ContratBcbgfh objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ContratBcbgfh
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
