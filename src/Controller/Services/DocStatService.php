<?php

namespace App\Controller\Services;

use App\Entity\Admin\Exercice;
use App\Entity\Admin\LogSnvlt;
use App\Entity\DocStats\Entetes\Documentbrh;
use App\Entity\DocStats\Entetes\Documentcp;
use App\Entity\DocStats\Pages\Pagebrh;
use App\Entity\DocStats\Pages\Pagecp;
use App\Entity\DocStats\Saisie\Lignepagebrh;
use App\Entity\DocStats\Saisie\Lignepagecp;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\DocBlock\Description;

class DocStatService
{
    public function __construct(private ManagerRegistry $registry)
    {

    }


    //Ajoute l'action utilisateur dans le fichier log
    public function save_action(
        User $user,
        string $table_concernee,
        string $action,
        \DateTimeImmutable $created_at,
        string $description
    )
    {
        $logsnvlt = new LogSnvlt();

        $logsnvlt->setAction($action);
        $logsnvlt->setTableConceree($table_concernee);
        $logsnvlt->setCreatedBy($user);
        $logsnvlt->setCreatedAt($created_at);
        $logsnvlt->setDescription($description);

        $this->registry->getManager()->persist($logsnvlt);
        $this->registry->getManager()->flush();
    }

    function getVolumeCp(Documentcp $documentcp):float
    {
        $volumecp = 0;

            $pagecp =$this->registry->getRepository(Pagecp::class)->findBy(['code_doccp'=>$documentcp]);
            foreach ($pagecp as $page){
                $lignepages = $this->registry->getRepository(Lignepagecp::class)->findBy(['code_pagecp'=>$page]);
                foreach ($lignepages as $ligne){
                    $volumecp = $volumecp +  $ligne->getVolumeArbrecp();
                }
            }
            return $volumecp;
    }

    function getVolumeBrh(Documentbrh $documentbrh):float
    {
        $volumebrh = 0;

        $pagebrh =$this->registry->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$documentbrh]);
        foreach ($pagebrh as $page){
            $lignepages = $this->registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$page]);
            foreach ($lignepages as $ligne){
                $volumebrh = $volumebrh +  $ligne->getCubageLignepagebrh();
            }
        }
        return $volumebrh;
    }

}