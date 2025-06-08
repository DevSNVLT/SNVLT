<?php

namespace App\Controller\Services\synchro\Mouvements\Documents\Documentcp;

use App\Controller\Services\synchro\ConnectionAlt;
use App\Controller\Services\Utils;
use App\Entity\Autorisation\Attribution;
use App\Entity\DocStats\Entetes\Documentcp;
use App\Entity\DocStats\Pages\Pagecp;
use App\Entity\DocStats\Saisie\Lignepagecp;
use App\Entity\References\Essence;
use App\Entity\References\Foret;
use Doctrine\Persistence\ManagerRegistry;

class DocumentcpSnvlt1
{
    public function __construct(private ConnectionAlt $connectionAlt,private ManagerRegistry $registry, private Utils $utils)
    {
    }

    public function AddDoccp(Documentcp $doccp){
        $docname = "CP";
        $fini = 'False';
        $valeur = 'VIDE';

            $sql = "INSERT INTO deif.documentcp(
                     id_doccp,
                     numero_doccp,
                     delivre_doccp,
                     code_perimetre,
                     exercice,
                     docname,
                     valeur,
                     date_saisie,
                     util_nom,
                     fini) 
            VALUES (" . $doccp->getId() . ",
                    '" . $doccp->getNumeroDoccp() . "',
                    '" . $doccp->getDelivreDoccp()->format("Y-m-d")  . "',
                    '" . $doccp->getCodeReprise()->getId()  . "', 
                    " . $doccp->getExercice()->getAnnee() . ",
                    '". $docname . "',
                    '" . $valeur."',
                      '". $doccp->getCreatedAt()->format("Y-m-d") . "',
                      '". str_replace("'", "''", $doccp->getCreatedBy()) . "',
                       '". $fini . "');";

        $this->connectionAlt->connect($sql);

        // Génération des pages
        $this->AddAllPages($doccp);

    }
    public function AddAllPages(Documentcp $doccp){

        $pages = $this->registry->getRepository(Pagecp::class)->findBy(
            ['code_doccp'=>$doccp]
        );
        foreach ($pages as $page){
            $fini = 'False';

            $sql = "INSERT INTO deif.pagecp(
                     id_pagecp,
                     numero_pagecp,
                     order_pagecp,
                     code_doccp,
                     numpage_cp,
                     fini) 
            VALUES (" . $page->getId() . ",
                    '" . $page->getIndex() . "',
                    '" . $page->getIndex() . "',
                    '" . $page->getCodeDoccp()->getId()  . "', 
                    ". $page->getNumeroPagecp() . ",
                       '". $fini . "');";

            $this->connectionAlt->connect($sql);
        }
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
    public function DeleteDoccp(Documentcp $doccp){
            $pages = $this->registry->getRepository(Pagecp::class)->findBy(
                ['code_doccp'=>$doccp]
            );
            foreach ($pages as $page){
                $lignes = $this->registry->getRepository(Lignepagecp::class)->findBy(
                    ['code_pagecp'=>$doccp]
                );
                foreach ($lignes as $ligne){
                    $this->DeleteLignePageDoccp($ligne);
                }
                $this->DeletePageDoccp($page);
            }
        $sql = "DELETE FROM deif.documentcp  WHERE id_documentcp=". $doccp->getId().";" ;
        $this->connectionAlt->connect($sql);
    }

    public function DeletePageDoccp(Pagecp $page){
        $sql = "DELETE FROM deif.pagecp  WHERE id_pagecp=". $page->getId().";" ;
        $this->connectionAlt->connect($sql);
    }
    public function DeleteLignePageDoccp(Lignepagecp $lignepagecp){
        $sql = "DELETE FROM deif.lignepagecp  WHERE id_lignepagecp=". $lignepagecp->getId().";" ;
        $this->connectionAlt->connect($sql);
    }

    public function EditDoccp(Documentcp $doccp){
        $sql = "UPDATE deif.documentcp
                SET numero_doccp='". $doccp->getNumeroDoccp(). "',
                    numero_doccp='". $doccp->getNumeroDoccp(). "',
                    delivre_doccp='". $doccp->getDelivreDoccp()->format("Y-m-d"). "'
                    WHERE id_documentcp=". $doccp->getId().";" ;
        $this->connectionAlt->connect($sql);
    }
    public function EditDoccpReprise(Documentcp $doccp){
        $sql = "UPDATE deif.documentcp
                SET numero_doccp='". $doccp->getNumeroDoccp(). "',
                    numero_doccp='". $doccp->getDelivreDoccp()->format("Y-m-d"). "'
                    WHERE id_documentcp=". $doccp->getId().";" ;
        $this->connectionAlt->connect($sql);
    }

    public function EditPageDoccp(Pagecp $page){

        $fini="False";
        $annee = "";
        $mois = "";

        if ($page->isFini()) { $fini = "True";}
        if ($page->getMois() && $page->getAnnee()){
            if (strlen($page->getMois()) == 1) { $mois = $page->getAnnee() . "-0". $page->getMois(). "-01";} else
            {$mois = $page->getAnnee() . "-". $page->getMois(). "-01";}
            $annee = $mois;
        }


        $sql = "UPDATE deif.pagecp
                SET numero_pagecp=". $page->getIndex() . ",
                    order_pagecp='". $page->getIndex() .  "',
                    mois_pagecp='". $mois . "',
                    annee_pagecp='". $annee . "',
                    village_pagecp='". $this->utils->removeSpecialCharacters($page->getVillagePagecp()) . "',
                    code_doccp='". $page->getCodeDoccp()->getId() . "',
                    numpage_cp='". $page->getNumeroPagecp() . "',
                    fini='". $fini . "'
                    WHERE id_pagecp=". $page->getId().";" ;
        $this->connectionAlt->connect($sql);
    }

    public function AddSinglePageDoccp(Pagecp $page){

        $fini="False";
        $fini="False";
        $annee = "";
        $mois = "";

        if ($page->isFini()) { $fini = "True";}
        if ($page->getMois() && $page->getAnnee()){
            if (strlen($page->getMois()) == 1) { $mois = $page->getAnnee() . "-0". $page->getMois(). "-01";} else
            {$mois = $page->getAnnee() . "-". $page->getMois(). "-01";}
            $annee = $mois;
        }




        if ($page->isFini()) { $fini = "True";}


        $sql = "INSERT INTO deif.pagecp(
                    id_pagecp, numero_pagecp, order_pagecp, mois_pagecp, annee_pagecp, 
                    village_pagecp,  code_doccp, numpage_cp, fini))
                 VALUES (
                         ". $page->getId() . ",
                         ". $page->getIndex() . ",
                         ". $page->getIndex() . ",
                         ". $mois. ",
                         ". $annee. ",
                         '". $this->utils->removeSpecialCharacters($page->getVillagePagecp()) . "',
                         ". $page->getCodeDoccp()->getId() . ",
                         '". $page->getNumeroPagecp() . "',
                         '". $fini . "'
                         
                 );";
        $this->connectionAlt->connect($sql);
    }

    /*    *****************************************************************************************************
   *                                                                                                   *
   *                                       LIGNE PAGE CP                                                    *
   *                                                                                                   *
   ******************************************************************************************************/


    public function EditLignePageDoccp(Lignepagecp $lignepagecp){
        $ecart = 0;
        $Autilise = "False";
        $Aabandonne = "False";
        $Butilise = "False";
        $BAbandonne = "False";
        $Cutilise = "False";
        $Cabandonne = "False";
        $FutAbandonne = "False";


        if ($lignepagecp->isAUtlise()) {$Autilise = "True";}
        if ($lignepagecp->isBUtilise()) {$Butilise = "True";}
        if ($lignepagecp->isCUtilise()) {$Cutilise = "True";}
        if ($lignepagecp->isAAbandon()) {$Aabandonne = "True";}
        if ($lignepagecp->isBAbandon()) {$BAbandonne = "True";}
        if ($lignepagecp->isCAbandon()) {$Cabandonne = "True";}
        if ($lignepagecp->isFutAbandon()) {$FutAbandonne = "True";}



        $sql = "UPDATE deif.lignepagecp
   SET numero_arbrecp=". $lignepagecp->getNumeroArbrecp() . ",
       zh_arbrecp=". $lignepagecp->getNumeroArbrecp() . ",
       x_arbrecp=". $lignepagecp->getNumeroArbrecp() . ",
       y_arbrecp=". $lignepagecp->getNumeroArbrecp() . ", jour_abattage=?, numero_essencecp=?, nom_essencecp=?, 
       longeur_arbrecp=?, diametre_arbrecp=?, volume_arbrecp=?, longeura_billecp=?, 
       diametrea_billecp=?, volumea_billecp=?, longeurb_billecp=?, diametreb_billecp=?, 
       volumeb_billecp=?, longeurc_billecp=?, diametrec_billecp=?, volumec_billecp=?, 
       code_pagecp=?, exercice=?, volume_calcule=?, ecart_lng=?, date_saisie=?, 
       user_id=?, util_nom=?, a_utlise=?, b_utilise=?, c_utilise=?, 
       a_abandon=?, b_abandon=?, c_abandon=?, fut_abandon=?, motivation=?
        WHERE id_lignepagecp =". $lignepagecp->getId().";" ;

        $this->connectionAlt->connect($sql);
    }
    public function AddLignePageDoccp(Lignepagecp $lignepagecp){
        $ecart = 0;
        $Autilise = "False";
        $Aabandonne = "False";
        $Butilise = "False";
        $BAbandonne = "False";
        $Cutilise = "False";
        $Cabandonne = "False";
        $FutAbandonne = "False";


        if ($lignepagecp->isAUtlise()) {$Autilise = "True";}
        if ($lignepagecp->isBUtilise()) {$Butilise = "True";}
        if ($lignepagecp->isCUtilise()) {$Cutilise = "True";}
        if ($lignepagecp->isAAbandon()) {$Aabandonne = "True";}
        if ($lignepagecp->isBAbandon()) {$BAbandonne = "True";}
        if ($lignepagecp->isCAbandon()) {$Cabandonne = "True";}
        if ($lignepagecp->isFutAbandon()) {$FutAbandonne = "True";}


    $sql = "INSERT INTO deif.lignepagecp(
            id_lignepagecp, numero_arbrecp, zh_arbrecp, x_arbrecp, y_arbrecp, 
            jour_abattage, numero_essencecp, nom_essencecp, longeur_arbrecp, 
            diametre_arbrecp, volume_arbrecp, longeura_billecp, diametrea_billecp, 
            volumea_billecp, longeurb_billecp, diametreb_billecp, volumeb_billecp, 
            longeurc_billecp, diametrec_billecp, volumec_billecp, code_pagecp, 
            exercice, volume_calcule, ecart_lng, date_saisie, util_nom, 
            a_utlise, b_utilise, c_utilise, a_abandon, b_abandon, c_abandon, 
            fut_abandon, motivation)
    VALUES (". $lignepagecp->getId() . ",
            ". $lignepagecp->getNumeroArbrecp() . ",
            '". $lignepagecp->getZhArbrecp()->getZone() . "',
            ". $lignepagecp->getXArbrecp() . ",
            ". $lignepagecp->getYArbrecp() . ",
            ". $lignepagecp->getJourAbattage() . ",
            '". $lignepagecp->getNomEssencecp()->getNumeroEssence() . "',
            '". $lignepagecp->getNomEssencecp()->getNomVernaculaire() . "',
            ". $lignepagecp->getLongeurArbrecp() . ",
            ". $lignepagecp->getDiametreArbrecp() . ",
            ". $lignepagecp->getVolumeArbrecp() . ",
            ". $lignepagecp->getLongeuraBillecp() . ",
            ". $lignepagecp->getDiametreaBillecp() . ",
            ". $lignepagecp->getVolumeaBillecp() . ",
            ". $lignepagecp->getLongeurbBillecp() . ",
            ". $lignepagecp->getDiametrebBillecp() . ",
            ". $lignepagecp->getVolumebBillecp() . ",
            ". $lignepagecp->getLongeurcBillecp() . ",
            ". $lignepagecp->getDiametrecBillecp() . ",
            ". $lignepagecp->getVolumecBillecp() . ",
            ". $lignepagecp->getCodePagecp()->getId() . ",
            ". $lignepagecp->getCodePagecp()->getCodeDoccp()->getExercice()->getAnnee() . ",
            ". $lignepagecp->getVolumeArbrecp() . ",
            ". $ecart . ",
            '". $lignepagecp->getCreatedAt()->format("Y-m-d") . "',
            '". $this->utils->removeSpecialCharacters($lignepagecp->getCreatedBy()) . "',
           '".$Autilise . "',
            '". $Butilise . "',
            '". $Cutilise . "',
            '". $Aabandonne . "',
            '". $BAbandonne . "',
            '". $Cabandonne . "',
            '". $FutAbandonne . "',
            '". $lignepagecp->getMotivation() . "'
    );";

        $this->connectionAlt->connect($sql);
    }
}