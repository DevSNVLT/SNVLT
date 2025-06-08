<?php

namespace App\Controller\Services\synchro;

use App\Entity\References\Essence;
use App\Entity\References\Foret;

class ForetSnvlt1
{
    public function __construct(private ConnectionAlt $connectionAlt)
    {
    }

    public function Maj_Foret(Foret $foret, bool $isNew){
        $sql = "";
        $typeforet = "";

        if ($foret->getCodeTypeForet()->getId() == 1) {
            $typeforet = "PEF";
        } elseif ($foret->getCodeTypeForet()->getId() == 2) {
            $typeforet = "FC";
        }  elseif ($foret->getCodeTypeForet()->getId() == 3) {
            $typeforet = "PV";
        }
        //dd($typeforet);

        if(!$isNew){

       $sql = "UPDATE deif.perimetrenu SET numero_perimetrenu='" . $foret->getNumeroForet() . "', superficie_perimetrenu=" . $foret->getSuperficie() . ", date_decision='" . $foret->getDatePremiereAttribution()->format("Y-m-d") . "', 
                               denomination='" . $foret->getDenomination() . "', coderegion=" . $foret->getCodeCantonnement()->getCodeDr()->getId() . ", region='" . $foret->getCodeCantonnement()->getCodeDr()->getDenomination() . "', date_saisie='" . $foret->getCreatedAt()->format("Y-m-d") . "', 
                               util_nom='" . str_replace("'", "''", $foret->getCreatedBy())  . "', type_perimetre='" . $typeforet .  "', code_cantonnement=" . $foret->getCodeCantonnement()->getId() ." WHERE id_perimetrenu=". $foret->getId().";" ;
        } else {
            $sql = "INSERT INTO deif.perimetrenu(
            id_perimetrenu, numero_perimetrenu, superficie_perimetrenu, date_decision, denomination,
            coderegion, region, date_saisie, util_nom, type_perimetre, code_cantonnement) 
            VALUES (" . $foret->getId() . ", '" . $foret->getNumeroForet() . "', " . $foret->getSuperficie() . ", '" . $foret->getDatePremiereAttribution()->format("Y-m-d")  . "', 
            '" . $foret->getDenomination() . "', ". $foret->getCodeCantonnement()->getCodeDr()->getId() . ", '" . $foret->getCodeCantonnement()->getCodeDr()->getDenomination() ."', '". $foret->getCreatedAt()->format("Y-m-d")  . "',
             '" . str_replace("'", "''", $foret->getCreatedBy()) . "', '". $typeforet . "', ". $foret->getCodeCantonnement()->getId() . ");";
        }

        $this->connectionAlt->connect($sql);
    }

    public function MajAttributionForet(Foret $foret){
             $statut = "NON ATTRIBUE";

             if ($foret->isAttribue()){
                 $statut = "ATTRIBUE";
             }

            $sql = "UPDATE deif.perimetrenu SET statut ='" . $statut . "' WHERE id_perimetrenu=". $foret->getId().";" ;
            $this->connectionAlt->connect($sql);

    }
    public function RetraitAttributionForet(Foret $foret){
        $statut = "NON ATTRIBUE";
        $reprise = "False";


        $sql = "UPDATE deif.perimetrenu SET statut ='" . $statut . "', reprise_att='" . $reprise .  "'  WHERE id_perimetrenu=". $foret->getId().";" ;
        $this->connectionAlt->connect($sql);

    }
    public function MajRepriseAttributionForet(Foret $foret){

        $statut = "ATTRIBUE";
        $reprise = "True";

        $sql = "UPDATE deif.perimetrenu SET statut ='" . $statut . "', reprise_att='" . $reprise .  "'  WHERE id_perimetrenu=". $foret->getId().";" ;
        $this->connectionAlt->connect($sql);

    }
    public function Delete_Foret(Foret $foret){

            $sql = "DELETE FROM deif.perimetrenu  WHERE id_perimetrenu=". $foret->getId().";" ;
            $this->connectionAlt->connect($sql);
    }
}