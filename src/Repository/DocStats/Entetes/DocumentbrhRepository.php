<?php

namespace App\Repository\DocStats\Entetes;

use App\Entity\DocStats\Entetes\Documentbrh;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Documentbrh>
 *
 * @method Documentbrh|null find($id, $lockMode = null, $lockVersion = null)
 * @method Documentbrh|null findOneBy(array $criteria, array $orderBy = null)
 * @method Documentbrh[]    findAll()
 * @method Documentbrh[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentbrhRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Documentbrh::class);
    }

//    /**
//     * @return Documentbrh[] Returns an array of Documentbrh objects
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

//    public function findOneBySomeField($value): ?Documentbrh
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
