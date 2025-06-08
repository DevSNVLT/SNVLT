<?php

namespace App\Services;

use App\Entity\DocStats\Pages\Pagebrh;
use App\Entity\DocStats\Saisie\Lignepagebrh;
use App\Repository\Blog\CategoryPublicationRepository;
use App\Repository\Blog\OptionRepository;
use Doctrine\Persistence\ManagerRegistry;


class OptionsService
{
    public function __construct(
        private OptionRepository $repository,
        private CategoryPublicationRepository $infos_publiques,
        private ManagerRegistry $registry
    )
    {

    }

    public function findAll()
    {
        return $this->repository->findAllForTwig();
    }

    public function findValue(string $name)
    {

        return $this->repository->getValue($name);
    }

    public function findInfosPubliques()
    {
        return $this->infos_publiques->findAll();
    }
	
	public function find_foret(string $numero_arbre, int $feuillet)
    {
        $ligne = new Lignepagebrh();
        $pagebrh = $this->registry->getRepository(Pagebrh::class)->find($feuillet);
        if ($pagebrh){
            $ligne = $this->registry->getRepository(Lignepagebrh::class)->findOneBy(['code_pagebrh'=>$pagebrh, 'numero_lignepagebrh'=>$numero_arbre]);
        }
        return $ligne;
    }
}