<?php

namespace App\Controller\Services\synchro\Mouvements;

use App\Controller\Services\synchro\ConnectionAlt;
use App\Entity\Autorisation\Attribution;
use App\Entity\References\Essence;
use App\Entity\References\Foret;

class AttributionSnvlt1
{
    public function __construct(private ConnectionAlt $connectionAlt)
    {
    }

    public function Ajout_Attribution(Attribution $attribution){
        $sql = "";
        $statut = "True";
        $reprise = "False";

        if ($attribution->isReprise()) { $reprise = "True";}

            $sql = "INSERT INTO deif.attribution(
                     id_attribution,
                     numero_decision,
                     date_decision,
                     code_exploitant,
                     exercice,
                     code_perimetrenu,
                     statut,
                     numero_perimetre_att,
                     superficie_pef,
                     date_saisie, util_nom, reprise) 
            VALUES (" . $attribution->getId() . ",
                    '" . $attribution->getNumeroDecision() . "',
                    '" . $attribution->getDateDecision()->format("Y-m-d")  . "',
                    '" . $attribution->getCodeExploitant()->getId()  . "', 
                    " . $attribution->getExercice()->getAnnee() . ",
                    ". $attribution->getCodeForet()->getId() . ",
                    '" . $statut."',
                    '". $attribution->getCodeForet()->getDenomination()  . "',
                     " . $attribution->getCodeForet()->getSuperficie() . ",
                      '". $attribution->getCreatedAt()->format("Y-m-d") . "',
                      '". str_replace("'", "''", $attribution->getCreatedBy()) . "',
                       '". $reprise . "');";


        $this->connectionAlt->connect($sql);
    }


    public function RetraitAttribution(Attribution $attribution){
        $statut = "False";
        $reprise = "False";

        $sql = "UPDATE deif.attribution SET statut ='" . $statut . "', reprise='" . $reprise .  "' WHERE id_attribution =". $attribution->getId().";" ;
        $this->connectionAlt->connect($sql);
    }

    public function RestaurerAttribution(Attribution $attribution){
        $statut = "ATTRIBUE";


        $sql = "UPDATE deif.attribution SET statut ='" . $statut . "' WHERE id_attribution=". $attribution->getId().";" ;
        $this->connectionAlt->connect($sql);

    }

    public function MajRepriseAtt(Attribution $attribution, bool $reprise){
        $reprise_att = "ATTRIBUE";
        if ($reprise){$reprise_att = "True";}

        $sql = "UPDATE deif.attribution SET reprise ='" . $reprise_att . "' WHERE id_attribution=". $attribution->getId().";" ;
        $this->connectionAlt->connect($sql);

    }
    public function Delete_Attribution(Attribution $attribution){

            $sql = "DELETE FROM deif.attribution  WHERE id_attribution=". $attribution->getId().";" ;
            $this->connectionAlt->connect($sql);
    }
}