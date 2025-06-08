<?php

namespace App\Repository\DocStats\Saisie;

use App\Entity\DocStats\Saisie\Lignepagersdpf;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lignepagersdpf>
 *
 * @method Lignepagersdpf|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lignepagersdpf|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lignepagersdpf[]    findAll()
 * @method Lignepagersdpf[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LignepagersdpfRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lignepagersdpf::class);
    }

//    /**
//     * @return Lignepagersdpf[] Returns an array of Lignepagersdpf objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Lignepagersdpf
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
