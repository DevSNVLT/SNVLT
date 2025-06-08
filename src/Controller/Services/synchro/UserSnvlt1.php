<?php

namespace App\Controller\Services\synchro;

use App\Entity\References\Essence;
use App\Entity\References\Exploitant;
use App\Entity\References\Foret;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class UserSnvlt1
{
    public function __construct(private ConnectionAlt $connectionAlt,private ManagerRegistry $registry )
    {
    }

    public function Ajout_User(User $user, $password){
        $role_user = 0;
        $statut = "False";
        $actif = "False";
        $agent_sodef = "False";
        $nouveau = "False";
        if ($user->getCodeDirection() or $user->getCodeDr() or $user->getCodeCantonnement() or $user->getCodeDdef() or $user->getCodePosteControle()){
            $role_user = 2;
        } else if ($user->getCodeexploitant()){
            $role_user = 1;
        } else if ($user->getCodeindustriel()){
            $role_user = 3;
        }

        //Vérifie les types getId()
        if ($user->getCodeGroupe()){ $groupe = $user->getCodeGroupe()->getId();} else {$groupe = 0;}
        if ($user->getCodeexploitant()){ $exploitant = $user->getCodeexploitant()->getNumeroExploitant();} else {$exploitant = 0;}
        if ($user->getCodeindustriel()){ $industriel = $user->getCodeindustriel()->getId();} else {$industriel = 0;}
        if ($user->getCodeService()){ $srv = $user->getCodeService()->getId();} else {$srv = 0;}
        if ($user->getCodeDirection()){ $direction = $user->getCodeDirection()->getId();} else {$direction = 0;}
        if ($user->getStatut()){ $statut = "True";}
        if ($user->getActif()){ $actif = "True";}
        if ($user->getNouveau()){ $nouveau = "True";}
        if ($user->getAgentSodef()){ $agent_sodef = "True";}


        $sql = "INSERT INTO deif.utilisateur(
            id_utilisateur, nom_utilisateur, prenoms_utilisateur, email_utilisateur, 
            mdp_utilisateur, code_groupe, statut, codeexploitant, 
            role_user, code_service, code_direction, agent_sodef, codeindustriel, 
            actif, nouveau, mobile)
    VALUES (". $user->getId() . ",
                '". $user->getNomUtilisateur() . "',
                '". $user->getPrenomsUtilisateur() . "',
                '". $user->getEmail() . "',
                '". $password . "',
                ". $groupe . ",
                '". $statut . "',
                ". $exploitant. ",
                ". $role_user . ",
                ". $srv . ",
                ". $direction . ",
                '". $agent_sodef . "',
                ". $industriel . ",
                '". $actif . "',
                '". $nouveau . "',
                '". $user->getMobile() . "');";

        $this->connectionAlt->connect($sql);
    }
    public function MAJ_User(User $user){
            $role_user = 0;
            $statut = "False";
            $actif = "False";
            $agent_sodef = "False";
            $nouveau = "False";
            if ($user->getCodeDirection() or $user->getCodeDr() or $user->getCodeCantonnement() or $user->getCodeDdef() or $user->getCodePosteControle()){
                $role_user = 2;
            } else if ($user->getCodeexploitant()){
                $role_user = 1;
            } else if ($user->getCodeindustriel()){
                $role_user = 3;
            }

            //Vérifie les types getId()
            if ($user->getCodeGroupe()){ $groupe = $user->getCodeGroupe()->getId();} else {$groupe = 0;}
            if ($user->getCodeexploitant()){ $exploitant = $user->getCodeexploitant()->getNumeroExploitant();} else {$exploitant = 0;}
            if ($user->getCodeindustriel()){ $industriel = $user->getCodeindustriel()->getId();} else {$industriel = 0;}
            if ($user->getCodeService()){ $srv = $user->getCodeService()->getId();} else {$srv = 0;}
            if ($user->getCodeDirection()){ $direction = $user->getCodeDirection()->getId();} else {$direction = 0;}
            if ($user->getStatut()){ $statut = "True";}
            if ($user->getActif()){ $actif = "True";}
            if ($user->getNouveau()){ $nouveau = "True";}
            if ($user->getAgentSodef()){ $agent_sodef = "True";}

            $sql = "UPDATE deif.utilisateur
                SET nom_utilisateur='". $user->getNomUtilisateur() . "',
                    prenoms_utilisateur='". $user->getPrenomsUtilisateur() . "',     
                    email_utilisateur='". $user->getEmail() . "',     
                    code_groupe='". $groupe . "',     
                    statut='". $statut . "',     
                    codeexploitant='". $exploitant . "',     
                    role_user='". $role_user. "',     
                    code_service='". $srv . "',     
                    code_direction='". $direction . "',     
                    agent_sodef='". $agent_sodef . "',   
                    codeindustriel='". $industriel . "',     
                    actif='". $actif . "',     
                    nouveau='". $nouveau . "',     
                    mobile='". $user->getMobile() . "' WHERE id_utilisateur=". $user->getId().";";


            $this->connectionAlt->connect($sql);
        }

    public function Delete_User(User $user){

            $sql = "DELETE FROM deif.utilisateur  WHERE id_utilisateur=". $user->getId().";" ;
            $this->connectionAlt->connect($sql);
    }

    public function Change_Pwd_User(User $user, $password){

        $sql = "UPDATE  deif.utilisateur SET mdp_utilisateur = '" . $password . "' WHERE id_utilisateur=". $user->getId().";" ;
        $this->connectionAlt->connect($sql);
    }
    public function Change_Exploitant(User $user){
        if ($user->getCodeexploitant()){

                $sql = "UPDATE  deif.utilisateur SET codeexploitant = " . $user->getCodeexploitant()->getNumeroExploitant() . " WHERE id_utilisateur=". $user->getId().";" ;
                $this->connectionAlt->connect($sql);

        }

    }
}