<?php

namespace App\Controller\Services\synchro\Mouvements\Documents\Documentbrh;

use App\Controller\Services\synchro\ConnectionAlt;
use App\Controller\Services\Utils;
use App\Entity\Autorisation\Attribution;
use App\Entity\DocStats\Entetes\Documentbrh;
use App\Entity\DocStats\Pages\Pagebrh;
use App\Entity\DocStats\Saisie\Lignepagebrh;
use App\Entity\References\Essence;
use App\Entity\References\Foret;
use Doctrine\Persistence\ManagerRegistry;

class DocumentbrhSnvlt1
{
    public function __construct(
        private ConnectionAlt $connectionAlt,
        private ManagerRegistry $registry,
        private Utils $utils
    )
    {
    }

    public function AddDocbrh(Documentbrh $docbrh){
        $docname = "BRH";
        $fini = 'False';
        $valeur = 'VIDE';

            $sql = "INSERT INTO deif.documentbrh(
                     id_docbrh,
                     numero_docbrh,
                     delivre_docbrh,
                     code_perimetre,
                     exercice,
                     docname,
                     valeur,
                     date_saisie,
                     util_nom,
                     fini) 
            VALUES (" . $docbrh->getId() . ",
                    '" . $docbrh->getNumeroDocbrh() . "',
                    '" . $docbrh->getDelivreDocbrh()->format("Y-m-d")  . "',
                    '" . $docbrh->getCodeReprise()->getId()  . "', 
                    " . $docbrh->getExercice()->getAnnee() . ",
                    '". $docname . "',
                    '" . $valeur."',
                      '". $docbrh->getCreatedAt()->format("Y-m-d") . "',
                      '". $this->utils->removeSpecialCharacters($docbrh->getCreatedBy()) . "',
                       '". $fini . "');";

        $this->connectionAlt->connect($sql);

        // Génération des pages
        $this->AddAllPages($docbrh);

    }


    public function DeleteDocbrh(Documentbrh $docbrh){
            $pages = $this->registry->getRepository(Pagebrh::class)->findBy(
                ['code_docbrh'=>$docbrh]
            );
            foreach ($pages as $page){
                $lignes = $this->registry->getRepository(Lignepagebrh::class)->findBy(
                    ['code_pagebrh'=>$docbrh]
                );
                foreach ($lignes as $ligne){
                    $this->DeleteLignePageDocbrh($ligne);
                }
                $this->DeletePageDocbrh($page);
            }
        $sql = "DELETE FROM deif.documentbrh  WHERE id_documentbrh=". $docbrh->getId().";" ;
        $this->connectionAlt->connect($sql);
    }




    public function EditDocbrh(Documentbrh $docbrh){
        $sql = "UPDATE deif.documentbrh
                SET numero_docbrh='". $docbrh->getNumeroDocbrh(). "',
                    numero_docbrh='". $docbrh->getNumeroDocbrh(). "',
                    delivre_docbrh='". $docbrh->getDelivreDocbrh()->format("Y-m-d"). "'
                    WHERE id_documentbrh=". $docbrh->getId().";" ;
        $this->connectionAlt->connect($sql);
    }
    public function EditDocbrhReprise(Documentbrh $docbrh){
        $sql = "UPDATE deif.documentbrh
                SET numero_docbrh='". $docbrh->getNumeroDocbrh(). "',
                    numero_docbrh='". $docbrh->getDelivreDocbrh()->format("Y-m-d"). "'
                    WHERE id_documentbrh=". $docbrh->getId().";" ;
        $this->connectionAlt->connect($sql);
    }

/*    *****************************************************************************************************
    *                                                                                                   *
    *                                       PAGE BRH                                                    *
    *                                                                                                   *
    ******************************************************************************************************/

    public function AddAllPages(Documentbrh $docbrh){

        $pages = $this->registry->getRepository(Pagebrh::class)->findBy(
            ['code_docbrh'=>$docbrh]
        );
        foreach ($pages as $page){
            $fini = 'False';

            $sql = "INSERT INTO deif.pagebrh(
                     id_pagebrh,
                     numero_pagebrh,
                     code_docbrh,
                     numpage_brh,
                     fini) 
            VALUES (" . $page->getId() . ",
                    '" . $page->getindex_page() . "',
                    '" . $page->getCodeDocbrh()->getId()  . "', 
                    ". $page->getNumeroPagebrh() . ",
                       '". $fini . "');";

            $this->connectionAlt->connect($sql);
        }
    }
    public function DeletePageDocbrh(Pagebrh $page){
        $sql = "DELETE FROM deif.pagebrh  WHERE id_pagebrh=". $page->getId().";" ;
        $this->connectionAlt->connect($sql);
    }

    public function EditPageDocbrh(Pagebrh $page){
        $usine = "";
        $fini="False";
        $confirmation_usine ="False";

        if ($page->getParcUsineBrh() ) {
            if ($page->getParcUsineBrh()->getSigle()){
                $usine = $page->getParcUsineBrh()->getSigle();
            } else {
                $usine = $page->getParcUsineBrh()->getRaisonSocialeUsine();
            }
        }

        if ($page->isFini()) { $fini = "True";}
        if ($page->isConfirmationUsine()) { $confirmation_usine = "True";}

        $sql = "UPDATE deif.pagebrh
                SET numero_pagebrh=". $page->getindex_page() . ",
                    destination_pagebrh='". $page->getDestinationPagebrh() .  "',
                    parc_usine_brh='". $this->utils->removeSpecialCharacters($usine) . "',
                    village_pagebrh='". $this->utils->removeSpecialCharacters($page->getVillagePagebrh()) . "',
                    chauffeurbrh='". $this->utils->removeSpecialCharacters($page->getChauffeurbrh()) . "',
                    date_chargementbrh='". $page->getDateChargementbrh()->format('Y-m-d') . "',
                    cout_transportbrh=". $page->getCoutTransportbrh() . ",
                    code_docbrh=". $page->getCodeDocbrh()->getId() . ",
                    numpage_brh='". $page->getNumeroPagebrh() . "',
                    immatcamion='". $page->getImmatcamion() . "',
                    code_usine_dest=". $page->getParcUsineBrh()->getId() . ",
                    fini='". $fini . "',
                    confirmation_usine='". $confirmation_usine . "'
                    WHERE id_pagebrh=". $page->getId().";" ;
        $this->connectionAlt->connect($sql);
    }

    public function AddSinglePageDocbrh(Pagebrh $page){
        $usine = "";
        $fini="False";
        $confirmation_usine ="False";

        if ($page->getParcUsineBrh() ) {
            if ($page->getParcUsineBrh()->getSigle()){
                $usine = $page->getParcUsineBrh()->getSigle();
            } else {
                $usine = $page->getParcUsineBrh()->getRaisonSocialeUsine();
            }
        }

        if ($page->isFini()) { $fini = "True";}
        if ($page->isConfirmationUsine()) { $confirmation_usine = "True";}


        $sql = "INSERT INTO deif.pagebrh(
                    id_pagebrh, numero_pagebrh, destination_pagebrh, parc_usine_brh, 
                    village_pagebrh, chauffeurbrh, date_chargementbrh, 
                    cout_transportbrh, code_docbrh, numpage_brh, immatcamion, code_usine_dest, fini,
                    confirmation_usine)
                 VALUES (
                         ". $page->getId() . ",
                         ". $page->getindex_page() . ",
                         '". $this->utils->removeSpecialCharacters($page->getDestinationPagebrh()) . "',
                         '". $this->utils->removeSpecialCharacters($usine) . "',
                         '". $this->utils->removeSpecialCharacters($page->getVillagePagebrh()) . "',
                         '". $this->utils->removeSpecialCharacters($page->getChauffeurbrh()) . "',
                         '". $page->getDateChargementbrh()->format('Y-m-d') . "',
                         ". $page->getCoutTransportbrh() . ",
                         ". $page->getCodeDocbrh()->getId() . ",
                         '". $page->getNumeroPagebrh() . "',
                         '". $page->getImmatcamion() . "',
                         ". $page->getParcUsineBrh()->getId() . ",
                         '". $fini . "',
                         '". $confirmation_usine . "',
                         
                 );";
        $this->connectionAlt->connect($sql);
    }
    /*    *****************************************************************************************************
    *                                                                                                   *
    *                                       LIGNE PAGE BRH                                                    *
    *                                                                                                   *
    ******************************************************************************************************/

    public function DeleteLignePageDocbrh(Lignepagebrh $lignepagebrh){
        $sql = "DELETE FROM deif.lignepagebrh  WHERE id_lignepagebrh=". $lignepagebrh->getId().";" ;
        $this->connectionAlt->connect($sql);
    }
    public function EditLignePageDocbrh(Lignepagebrh $lignepagebrh){
        $ecart = 0;

        $sql = "UPDATE deif.lignepagebrh
                   SET nom_essencebrh='". $lignepagebrh->getNomEssencebrh()->getNomVernaculaire(). "',
                   numero_lignepagebrh=". $lignepagebrh->getNumeroLignepagebrh(). ",
                   zh_lignepagebrh='". $lignepagebrh->getZhLignepagebrh()->getZone(). "',
                   x_lignepagebrh=". $lignepagebrh->getXLignepagebrh(). ",
                   y_lignepagebrh=". $lignepagebrh->getYLignepagebrh(). ",
                   lettre_lignepagebrh='". $lignepagebrh->getLettreLignepagebrh(). "',
                   longeur_lignepagebrh=". $lignepagebrh->getLongeurLignepagebrh(). ",
                   diametre_lignepagebrh=". $lignepagebrh->getDiametreLignepagebrh(). ",
                   cubage_lignepagebrh=". $lignepagebrh->getCubageLignepagebrh(). ",
                   observationbrh='". $lignepagebrh->getObservationbrh(). "',
                   code_pagebrh=". $lignepagebrh->getCodePagebrh()->getId(). ",
                   cubage=". $lignepagebrh->getCubageLignepagebrh(). ",
                   ecart=". $ecart. ",
                   exercice=". $lignepagebrh->getExercice()->getAnnee(). ",
                   date_saisie='".$lignepagebrh->getCreatedAt()->format('Y-m-d'). "',
                   util_nom='".  $this->utils->removeSpecialCharacters($lignepagebrh->getCreatedBy()). "'
                   WHERE id_lignepagebrh =". $lignepagebrh->getId().";" ;

        $this->connectionAlt->connect($sql);
    }
    public function AddLignePageDocbrh(Lignepagebrh $lignepagebrh){
        $ecart = 0;

        $sql = "INSERT INTO deif.lignepagebrh(
                              id_lignepagebrh,
                              nom_essencebrh,
                              numero_lignepagebrh,
                              zh_lignepagebrh, 
                              x_lignepagebrh,
                              y_lignepagebrh,
                              lettre_lignepagebrh,
                              longeur_lignepagebrh, 
                              diametre_lignepagebrh,
                              cubage_lignepagebrh,
                              observationbrh,
                              code_pagebrh, 
                              cubage,
                              ecart,
                              exercice,
                              date_saisie,
                              util_nom)
                            VALUES (". $lignepagebrh->getId(). ",
                                    '". $lignepagebrh->getNomEssencebrh()->getNomVernaculaire(). "',
                                    ". $lignepagebrh->getNumeroLignepagebrh(). ",
                                    '". $lignepagebrh->getZhLignepagebrh()->getZone(). "',
                                    ". $lignepagebrh->getXLignepagebrh(). ",
                                    ". $lignepagebrh->getYLignepagebrh(). ",
                                    '". $lignepagebrh->getLettreLignepagebrh(). "',
                                    ". $lignepagebrh->getLongeurLignepagebrh(). ",
                                    ". $lignepagebrh->getDiametreLignepagebrh(). ",
                                    ". $lignepagebrh->getCubageLignepagebrh(). ",
                                    '". $lignepagebrh->getObservationbrh(). "',
                                    ". $lignepagebrh->getCodePagebrh()->getId(). ",
                                    ". $lignepagebrh->getCubageLignepagebrh(). ",
                                    ". $ecart. ",
                                    ". $lignepagebrh->getExercice()->getAnnee(). ",
                                    '". $lignepagebrh->getCreatedAt()->format('Y-m-d'). "',
                                    '". $this->utils->removeSpecialCharacters($lignepagebrh->getCreatedBy()). "'
                                    );";
        $this->connectionAlt->connect($sql);
    }

}