<?php

namespace App\Repository;

use App\Entity\MenuPermission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MenuPermission>
 *
 * @method MenuPermission|null find($id, $lockMode = null, $lockVersion = null)
 * @method MenuPermission|null findOneBy(array $criteria, array $orderBy = null)
 * @method MenuPermission[]    findAll()
 * @method MenuPermission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuPermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenuPermission::class);
    }

    public function save(MenuPermission $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MenuPermission $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return MenuPermission[] Returns an array of MenuPermission objects
     */
    public function findOnlyParent($code_parent, $code_groupe): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.parent_menu = :code_parent')
            ->andWhere('m.code_groupe_id = :code_groupe')
            ->setParameter('code_groupe', $code_groupe)
            ->setParameter('code_parent', $code_parent)
            ->orderBy('m.parent_menu','ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return MenuPermission[] Returns an array of MenuPermission objects
     */
    public function findMenuByGroupe($id_groupe): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.code_groupe_id = :id_groupe')
            ->setParameter('id_groupe', $id_groupe)
            ->orderBy('m.nom_Menu','ASC')
            ->getQuery()
            ->getResult()
            ;
    }


    /**
     * @return MenuPermission[] Returns an array of MenuPermission objects
     */
    public function showMenuByGroupe($id_groupe): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.code_groupe_id = :id_groupe')
            ->setParameter('id_groupe', $id_groupe)
            ->orderBy('m.nom_Menu','ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function countMenuByGroupe($id_groupe):int
    {
        return $this->createQueryBuilder('m')
            ->select('count(m.id)')
            ->andWhere('m.code_groupe_id = :id_groupe')
            ->setParameter('id_groupe', $id_groupe)
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    public function PermissionDQL($id_groupe)
    {
        return $this->createQueryBuilder('m')
            ->select('m.nom_Menu')
            ->andWhere('m.code_groupe_id = :id_groupe')
            ->setParameter('id_groupe', $id_groupe)
            ->getQuery()
            ;
    }


    /**
     * @return MenuPermission
     */
    public function findByCodeMenuPermission($value): ?MenuPermission
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.numero_MenuPermission = :code')
            ->setParameter('code', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return MenuPermission[] Returns an array of MenuPermission objects
     */
    public function findMenuByGroupeAndMenu($id_groupe, $id_menu): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.code_groupe_id = :id_groupe')
            ->andWhere('m.id = :id_menu')
            ->setParameter('id_groupe', $id_groupe)
            ->setParameter('id_menu', $id_menu)
            ->orderBy('m.nom_Menu','ASC')
            ->getQuery()
            ->getResult()
            ;
    }


    public function CountMenuPermission()
    {
        return $this->createQueryBuilder('e')
            ->select('count(e.id_MenuPermission)')
            ->getQuery()
            ->getSingleResult()
            ;
    }
}
