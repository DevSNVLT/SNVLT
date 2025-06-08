<?php

namespace App\Controller\Services\synchro\Mouvements;

use App\Controller\Services\synchro\ConnectionAlt;
use App\Entity\Autorisation\Reprise;
use App\Entity\DocStats\Pages\Pagecp;
use App\Entity\References\Essence;
use App\Entity\References\Foret;

class RepriseSnvlt1
{
    public function __construct(private ConnectionAlt $connectionAlt)
    {
    }

    public function Ajout_Reprise(Reprise $reprise, bool $isNew){
        $sql = "";
        $actif= "False";

        if ($reprise->isStatut()) { $actif = "True";}
        if(!$isNew){

            $sql = "UPDATE deif.perimetre 
                SET numero_perimetre='" . $reprise->getCodeAttribution()->getCodeForet()->getDenomination() . "',
                    code_attribution=" . $reprise->getCodeAttribution()->getId() . ",
                    date_autorisation='" . $reprise->getDateAutorisation()->format('Y-m-d') . "',
                     exercice='" . $reprise->getExercice()->getAnnee() . "',
                     actif='" . $actif . "',
                     date_saisie='" . $reprise->getCreatedAt()->format("Y-m-d") . "', 
                     util_nom='" . str_replace("'", "''", $reprise->getCreatedBy())  . "' WHERE id_perimetre=". $reprise->getId().";" ;
        } else {
            $sql = "INSERT INTO deif.perimetre(
                     id_perimetre,
                     numero_perimetre,
                     code_attribution,
                     date_autorisation,   
                     exercice,
                     date_saisie,
                     util_nom,
                     actif) 
            VALUES (" . $reprise->getId() . ",
                    '" . $reprise->getCodeAttribution()->getCodeForet()->getDenomination() . "',
                    " . $reprise->getCodeAttribution()->getId() . ",
                    '" . $reprise->getDateAutorisation()->format('Y-m-d')  . "',
                    " . $reprise->getExercice()->getAnnee() . ", 
                    '" . $reprise->getCreatedAt()->format('Y-m-d') . "',
                    '". str_replace("'", "''", $reprise->getCreatedBy()) . "',
                    '" . $actif."');";
        }



        $this->connectionAlt->connect($sql);
    }


    public function RetraitReprise(Reprise $reprise){
        $statut = "False";

        $sql = "UPDATE deif.perimetre SET actif ='" . $statut . "' WHERE id_perimetre =". $reprise->getId().";" ;
        $this->connectionAlt->connect($sql);
    }

    public function RestaurerReprise(Reprise $reprise){
        $statut = "True";


        $sql = "UPDATE deif.perimetre SET actif ='" . $statut . "' WHERE id_perimetre=". $reprise->getId().";" ;
        $this->connectionAlt->connect($sql);

    }
    public function Delete_Reprise(Reprise $reprise){
            $sql = "DELETE FROM deif.perimetre  WHERE id_perimetre=". $reprise->getId().";" ;
            $this->connectionAlt->connect($sql);
    }

    public function EditPagecpNumber(Pagecp $pagecp){
        $sql = "UPDATE deif.pagecp SET numpage_cp = '". $pagecp->getNumeroPagecp(). "'  WHERE id_perimetre=". $pagecp->getId().";" ;
        $this->connectionAlt->connect($sql);
    }

}