<?php

namespace App\Controller\Services;

use App\Entity\Admin\Exercice;
use App\Entity\Admin\LogSnvlt;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\DocBlock\Description;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class AdministrationService
{
    public function __construct(private ManagerRegistry $registry, private RequestStack $request)
    {

    }
    //Renvoi l'exercice en cours
    public function getAnnee()
    {
        $exo = $this->request->getSession()->get("exercice");
        if ($exo){
            $exercice = $this->registry->getRepository(Exercice::class)->find($exo);
        } else {
            $exercice = $this->registry->getRepository(Exercice::class)->findOneBy(['cloture'=>false],['id'=>'DESC']);
        }

        return $exercice;
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

}