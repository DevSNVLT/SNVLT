<?php

namespace App\Controller\Services\synchro;

use App\Entity\References\Essence;
use App\Entity\References\Usine;

class UsineSnvlt1
{
    public function __construct(private ConnectionAlt $connectionAlt)
    {
    }

    public function Maj_Usine(Usine $usine, bool $isNew){
        $sql = "";
        $dr = "";
        $exp = "False";
        $code_exp = 0;
        $export = "False";


        // Rattachement Cantonnement
        if ($usine->getCodeCantonnement()){
            $cantonnement = $usine->getCodeCantonnement()->getId();
            if ($usine->getCodeCantonnement()->getCodeDr()){
                $dr = $usine->getCodeCantonnement()->getCodeDr()->getId();
            }
        } else {
            $cantonnement = 0;
        }

        // Rattachement Exploitant
        if ($usine->getCodeExploitant()){
            $code_exp = $usine->getCodeExploitant()->getId();
            $exp = "True";
        }

        if ($usine->isExport()){ $export = "True";}


        if(!$isNew){



            $sql = "UPDATE deif.usine
                    SET numero_usine=". $usine->getNumeroUsine() . ",
                    raison_sociale_usine='". $usine->getRaisonSocialeUsine() . "',  
                    responsable_usine='". $usine->getPersonneRessource() . "',     
                    email_responsable='". $usine->getEmailPersonneRessource() . "',     
                    mobile_responsable='". $usine->getMobilePersonneRessource() . "',     
                    adresse_usine='". $usine->getAdresseUsine() . "',     
                    ville='". $usine->getVille() . "',     
                    capacite_usine='". $usine->getCapaciteUsine() . "',     
                    cc_usine='". $usine->getCcUsine() . "',     
                    tel_usine='". $usine->getTelUsine(). "',     
                    code_cantonnement='". $cantonnement . "',     
                    dr='". $dr . "',     
                    code_exploitant='". $code_exp. "',     
                    exp='". $exp. "',     
                    export='". $export. "',     
                    code_exportateur='". $usine->getCodeExportateur(). "',     
                    sigle='". $usine->getSigle() . "' WHERE id_usine=". $usine->getId().";";
        } else {
            $sql = "INSERT INTO deif.usine(
            id_usine, numero_usine, raison_sociale_usine, responsable_usine, 
            email_responsable, mobile_responsable, adresse_usine, ville, capacite_usine, cc_usine, tel_usine,
            code_cantonnement, dr, code_exploitant, 
            exp, export, code_exportateur, sigle)
            VALUES (" . $usine->getId() . ",
                    ". $usine->getNumeroUsine() . ",
                    '" . $usine->getRaisonSocialeUsine() . "',
                    '" . $usine->getPersonneRessource()  . "', 
                    '" . $usine->getEmailPersonneRessource() . "',
                    '" . $usine->getMobilePersonneRessource() . "',
                    '". $usine->getAdresseUsine() . "',
                    '". $usine->getVille() . "',
                     ". $usine->getCapaciteUsine() . ",
                    '". $usine->getCcUsine() . "',
                    '". $usine->getTelUsine() . "',
                     ". $cantonnement . ",
                    '". $dr . "',
                     ". $code_exp . ",
                     ". $exp. ",
                    '". $export. "',
                    '". $usine->getCodeExportateur() . "',
                    '" . $usine->getSigle() ."');";
        }

        $this->connectionAlt->connect($sql);
    }

    public function Delete_Essence(Usine $usine){

            $sql = "DELETE FROM deif.usine  WHERE id_usine=". $usine->getId().";" ;
            $this->connectionAlt->connect($sql);
    }
}