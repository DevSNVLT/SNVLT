<?php

namespace App\Repository;

use App\Entity\Menu;
use App\Entity\MenuPermission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Menu>
 *
 * @method Menu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Menu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Menu[]    findAll()
 * @method Menu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuRepository extends ServiceEntityRepository
{
    private  $menuPermissionRepository;
    public function __construct(ManagerRegistry $registry, MenuPermissionRepository $menuPermissionRepository)
    {
        parent::__construct($registry, Menu::class);
        $this->menuPermissionRepository = $menuPermissionRepository;
    }

    public function save(Menu $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Menu $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Menu[] Returns an array of Menu objects
     */
    public function findOnlyParent(): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.parent_menu is NULL')
            ->orderBy('m.nom_menu','ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Menu
     */
    public function findByCodeMenu($value): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.parent_menu = :code')
            ->setParameter('code', $value)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findNotInPermissionAndByGroupe($id_groupe): array
    {
     //   MenuPermission $menuPermission = null;
        //$menuPermission = new MenuPermissionRepository();
        // Récupération des Todo lus
       /* $this->menuPermissionRepository->createQueryBuilder('permissions')
            ->leftJoin('permissions.', 'menu')
            ->where('p.code_groupe_id= :value')
        ;*/
        $menupermission = $this->menuPermissionRepository->PermissionDQL($id_groupe);
        return $this->createQueryBuilder('men')
                ->andWhere("men.classname_menu <> '' ")
                ->andWhere('men.nom_menu NOT IN (' . $menupermission->getDQL(). ')')
                ->setParameter('id_groupe', $id_groupe)
                ->orderBy('men.nom_menu', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function CountMenu()
    {
        return $this->createQueryBuilder('e')
            ->select('count(e.id_Menu)')
            ->getQuery()
            ->getSingleResult()
            ;
    }
}
