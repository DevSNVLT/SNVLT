<?php

namespace App\Controller\Services;

use App\Entity\Administration\FicheProspection;
use App\Entity\Administration\InventaireForestier;
use App\Entity\Administration\ProspectionTemp;
use App\Entity\References\Essence;
use App\Entity\References\ZoneHemispherique;
use App\Repository\Administration\ProspectionTempRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use League\Csv\Reader;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportProspectionService
{
    public function __construct(
        private LoggerInterface $logger,
        private ProspectionTempRepository $prospectionsTemp,
        private EntityManagerInterface $em,
         private ManagerRegistry $registry,
         private  Utils $utils)
    {

    }
    public function importCsvProspectionTemp(string $fichier, FicheProspection $code_prospection):void
    {


        $prospections = $this->readCsvFile($fichier);

        foreach ($prospections as $ArrayProspection){
            $prospection = $this->UpdateOrCreateInventaireTemp($ArrayProspection, $code_prospection);
            $this->em->persist($prospection);
        }

        $this->em->flush();

        $this->logger->info("Inventory data uploaded to Prospection Temp");
    }

    public function importCSV(SymfonyStyle $io):void
{
    $io->title('Importation du fichier prospection');
    $fichier = 'test-657c73efe0778.csv';
    $prospections = $this->readCsvFile($fichier);

    $io->progressStart(count($prospections));

    foreach ($prospections as $ArrayProspection){
        $io->progressAdvance();
        $prospection = $this->UpdateOrCreateInventaireTemp($ArrayProspection);
        $this->em->persist($prospection);
    }
    $this->em->flush();

    $io->progressFinish();

    $io->success('Importation terminée');
}

    public function readCsvFile(string $fichier): Reader
    {

        $csv = Reader::createFromPath($fichier, 'r+');
        //dd($csv);
        $csv->setHeaderOffset(0);

        return $csv;
    }

    public function UpdateOrCreateInventaireTemp(array $inventairetemp, FicheProspection $prospection): ProspectionTemp
    {
        //$inventairetemp = $this->prospectionsTemp->findOneBy([]);
        $prospectiontemp = new ProspectionTemp();

        //dd($inventairetemp['numero']);
        $prospectiontemp->setNumero($inventairetemp['numero']);
        $prospectiontemp->setForet($inventairetemp['foret']);
        $prospectiontemp->setCodeEssence($inventairetemp['code_essence']);
        $prospectiontemp->setZoneH($inventairetemp['zone_h']);
        $prospectiontemp->setX($inventairetemp['x']);
        $prospectiontemp->setY($inventairetemp['y']);
        $prospectiontemp->setLng($inventairetemp['lng']);
        $prospectiontemp->setDm($inventairetemp['dm']);
        if ($inventairetemp['lac'] == "O"){
            $prospectiontemp->setLac(true);
        } else {
            $prospectiontemp->setLac(false);
        }

        $prospectiontemp->setCodeFichep($prospection);

        $foret = $prospection->getCodeAttribution()->getCodeForet()->getDenomination();

        $valide_essence = $this->isEssenceValide($inventairetemp['code_essence']);
        $valide_zone = $this->isZoneValide($inventairetemp['zone_h']);


        $motif = "";

        // Vérifie si la foret fourni dans le fichier et celle dans le formulaire upload sont conforme
        if ($foret != $inventairetemp['foret']){
            $prospectiontemp->setHasError(true);
            $motif = $motif . " | " .  "FORET NON CONFORME AU FICHIER FOURNI";
        }

        // Vérifie si le code essence est présent en base
        if (!$valide_essence){
            $prospectiontemp->setHasError(true);
            $motif = $motif . " | " .  "CODE ESSENCE INCORRECT";
        }

        // Vérifie si la zone H est corecte
        if (!$valide_zone){
            $prospectiontemp->setHasError(true);
            $motif = $motif . " | " .  "ZONE HEMISPHERIQUE INCORRECTE";
        }

        // Vérifie si les coordonnées X et Y sont correctes
        $x = $inventairetemp['x'];
        $y = $inventairetemp['y'];

        if (strlen($x) != 6 or strlen($y) !=6){
            $prospectiontemp->setHasError(true);
            $motif = $motif . " | " .  "COORDONNEES CARTESIENNES INCORRECTES";
        }



        // Vérifie si la donnée est déja présente en base
                $essence = $this->registry->getRepository(Essence::class)->findOneBy(['numero_essence'=>$inventairetemp['code_essence']]);
                $zh = $this->registry->getRepository(ZoneHemispherique::class)->findOneBy(['zone'=>$inventairetemp['zone_h']]);
                $lng = $inventairetemp['lng'];
                $dm = $inventairetemp['dm'];

                if ($essence && $zh && $lng && $dm && $x & $y){
                    $recherche_donnee = $this->registry->getRepository(InventaireForestier::class)->findBy([
                        'code_essence'=>$essence,
                        'zoneh'=>$zh,
                        'lng'=>$lng,
                        'dm'=>$dm,
                        'x'=>$x,
                        'y'=>$y
                    ]);

                    if ($recherche_donnee){
                        $prospectiontemp->setHasError(true);
                        $motif = $motif . " | " .  "DONNEE PRESENTE EN BASE";
                    }
                }

        $prospectiontemp->setMotifError($motif);

        //Calcul du cubage
        $prospectiontemp->setVolume($this->utils->calcul_volume($inventairetemp['lng'], $inventairetemp['dm']));

        return $prospectiontemp;
    }

    public function isEssenceValide($code_essence):bool
    {
        $resultat = $this->registry->getRepository(Essence::class)->findOneBy(['numero_essence'=>$code_essence]);

        if($resultat){
            return  true;
        } else {
            return false;
        }
    }

    public function isZoneValide($zone):bool
    {
        $resultat = $this->registry->getRepository(ZoneHemispherique::class)->findOneBy(['zone'=>$zone]);

        if($resultat){
            return  true;
        } else {
            return false;
        }
    }
}