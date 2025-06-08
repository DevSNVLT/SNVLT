<?php

namespace App\Controller\Services;


use App\Entity\Blog\CategoryPublication;
use App\Repository\Blog\CategoryPublicationRepository;
use App\Repository\Blog\PublicationRepository;
use Doctrine\Persistence\ManagerRegistry;


class PublicInfosService
{
    public function __construct(
        private CategoryPublicationRepository $repository,
        private PublicationRepository $publications,
        private ManagerRegistry $registry
    )
    {

    }

    public function findAll()
    {
        return $this->repository->findAll();
    }
    public function findPublicationByCategorie($id_categorie)
    {
        $categorie = $this->registry->getRepository(CategoryPublication::class)->find($id_categorie);
        return $this->publications->findBy(['code_category'=>$categorie]);
    }
}