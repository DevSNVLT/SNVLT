<?php

namespace App\Repository\DocStats\Entetes;

use App\Entity\DocStats\Entetes\Documentrsdpf;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Documentrsdpf>
 *
 * @method Documentrsdpf|null find($id, $lockMode = null, $lockVersion = null)
 * @method Documentrsdpf|null findOneBy(array $criteria, array $orderBy = null)
 * @method Documentrsdpf[]    findAll()
 * @method Documentrsdpf[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentrsdpfRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Documentrsdpf::class);
    }

//    /**
//     * @return Documentrsdpf[] Returns an array of Documentrsdpf objects
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

//    public function findOneBySomeField($value): ?Documentrsdpf
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
