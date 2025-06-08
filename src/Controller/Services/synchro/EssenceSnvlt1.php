<?php

namespace App\Controller\Services\synchro;

use App\Entity\References\Essence;

class EssenceSnvlt1
{
    public function __construct(private ConnectionAlt $connectionAlt)
    {
    }

    public function Maj_Essence(Essence $essence, bool $isNew){
        $sql = "";
        if(!$isNew){
            $sql = "UPDATE deif.essence SET numero_essence='" . $essence->getNumeroEssence() . "', nom_vernaculaire='" . $essence->getNomVernaculaire() . "', famille_essence='" . $essence->getFamilleEssence() . "', 
                               nom_scientifique='" . $essence->getNomScientifique() . "', categorie_essence='" . $essence->getCategorieEssence() . "', taxe_abattage=" . $essence->getTaxeAbattage() . ", dm_minima=" . $essence->getDmMinima() . ", 
                               taxe_preservation=" . $essence->getTaxePreservation() . " WHERE id_essence=". $essence->getId().";" ;
        } else {
            $sql = "INSERT INTO deif.essence(
            id_essence, numero_essence, nom_vernaculaire, famille_essence, 
            nom_scientifique, categorie_essence, taxe_abattage, dm_minima, 
            taxe_preservation) 
            VALUES (" . $essence->getId() . ", '" . $essence->getNumeroEssence() . "', '" . $essence->getNomVernaculaire() . "', '" . $essence->getFamilleEssence() . "', 
            '" . $essence->getNomScientifique() . "', '" . $essence->getCategorieEssence() . "', " . $essence->getTaxeAbattage() . ", " . $essence->getDmMinima() . ", 
            " . $essence->getTaxePreservation() . ");";
        }

        $this->connectionAlt->connect($sql);
    }

    public function Delete_Essence(Essence $essence){

            $sql = "DELETE FROM deif.essence  WHERE id_essence=". $essence->getId().";" ;
            $this->connectionAlt->connect($sql);
    }
}