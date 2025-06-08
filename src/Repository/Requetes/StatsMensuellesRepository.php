<?php

namespace App\Repository\Requetes;

use App\Entity\Requetes\StatsMensuelles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StatsMensuelles>
 *
 * @method StatsMensuelles|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatsMensuelles|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatsMensuelles[]    findAll()
 * @method StatsMensuelles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class StatsMensuellesRepository  extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatsMensuelles::class);
    }

    public function save(StatsMensuelles $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(StatsMensuelles $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

}