<?php

namespace App\Repository\DocStats\Saisie;

use App\Entity\DocStats\Saisie\Lignepagebcbgfh;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lignepagebcbgfh>
 *
 * @method Lignepagebcbgfh|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lignepagebcbgfh|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lignepagebcbgfh[]    findAll()
 * @method Lignepagebcbgfh[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LignepagebcbgfhRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lignepagebcbgfh::class);
    }

//    /**
//     * @return Lignepagebcbgfh[] Returns an array of Lignepagebcbgfh objects
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

//    public function findOneBySomeField($value): ?Lignepagebcbgfh
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
