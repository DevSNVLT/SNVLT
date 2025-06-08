<?php

namespace App\Repository\Requetes;

use App\Entity\Requetes\CubageEssences;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CubageEssences>
 *
 * @method CubageEssences|null find($id, $lockMode = null, $lockVersion = null)
 * @method CubageEssences|null findOneBy(array $criteria, array $orderBy = null)
 * @method CubageEssences[]    findAll()
 * @method CubageEssences[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CubageEssencesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CubageEssences::class);
    }

    public function save(CubageEssences $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CubageEssences $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

}
