<?php

namespace App\Controller\Services\synchro;

use App\Entity\Groupe;

class GroupeSnvlt1
{
    public function __construct(private ConnectionAlt $connectionAlt)
    {
    }

    public function Maj_Groupe(Groupe $groupe, bool $isNew){
        $sql = "";


        if(!$isNew){
            $sql = "UPDATE deif.groupe SET nom_groupe='" . $groupe->getNomGroupe() . "' WHERE id_groupe =". $groupe->getId().";" ;
        } else {
            $sql = "INSERT INTO deif.groupe(
                    id_groupe, nom_groupe)
                VALUES (" . $groupe->getId() . ", '". $groupe->getNomGroupe() . "' );";
        }

        $this->connectionAlt->connect($sql);
    }

    public function Delete_Groupe(Groupe $groupe){

            $sql = "DELETE FROM deif.groupe  WHERE id_groupe=". $groupe->getId().";" ;
            $this->connectionAlt->connect($sql);
    }
}