<?php

namespace App\Repository\DocStats;

use App\Entity\DocStats\DocsStats;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DocsStats>
 *
 * @method DocsStats|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocsStats|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocsStats[]    findAll()
 * @method DocsStats[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocsStatsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocsStats::class);
    }

}
