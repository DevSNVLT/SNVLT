<?php

namespace App\Repository\DocStats\Entetes;

use App\Entity\DocStats\Entetes\Documentbcbgfh;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Documentbcbgfh>
 *
 * @method Documentbcbgfh|null find($id, $lockMode = null, $lockVersion = null)
 * @method Documentbcbgfh|null findOneBy(array $criteria, array $orderBy = null)
 * @method Documentbcbgfh[]    findAll()
 * @method Documentbcbgfh[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentbcbgfhRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Documentbcbgfh::class);
    }

//    /**
//     * @return Documentbcbgfh[] Returns an array of Documentbcbgfh objects
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

//    public function findOneBySomeField($value): ?Documentbcbgfh
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
