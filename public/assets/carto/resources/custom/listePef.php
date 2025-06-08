<?php

$host = "localhost";
$user = "postgres";
$password = "020780";
$dbname = "dpif2022";
$port = "5432";

$con = pg_connect("host=$host dbname=$dbname port=$port user=$user password=$password");

if(!$con){
    die("Connection failed.");
}

$request = "";

$searchTxt = $_POST['searchTxt'];
//$exercice = $_POST['exercice'];
$dateDebut = $_POST['dateDebut'];
$dateFin = $_POST['dateFin'];

// if(isset($_POST['request'])){
  // $request = $_POST['request'];
  // $searchTxt = $_POST['searchTxt'];
  // $exercice = $_POST['exercice'];
  // $searchAttribute = $_POST['searchAttribute'];
// }
// Fetch all records

	$query = "SELECT * FROM deif.all_brh WHERE numero_perimetre ='$searchTxt' AND date_chargementbrh BETWEEN '$dateDebut' AND '$dateFin'";
	//echo $query;
  $result = pg_query($con, $query);

  $response = array();
	
  while ($row = pg_fetch_assoc($result) ){

     $y_lignepagebrh = $row['y_lignepagebrh'];
	 $x_lignepagebrh = $row['x_lignepagebrh'] ;
	 $valueNumero = $row['numero_lignepagebrh'].$row['lettre_lignepagebrh'] ;
	 $zh_lignepagebrh = $row['zh_lignepagebrh'];
	 $longeur_lignepagebrh = $row['longeur_lignepagebrh'];
	 $diametre_lignepagebrh = $row['diametre_lignepagebrh'];
	 $cubage_lignepagebrh = $row['cubage_lignepagebrh'];
	 $nom_essencebrh = $row['nom_essencebrh'];
	 $exercice = $row['exercice'];
	 
     $response[] = array(
	 "Essence" => $nom_essencebrh,
	 "Numero" => $valueNumero,
	 "Zone" => $zh_lignepagebrh,
	 "x" => $x_lignepagebrh,
	 "y" => $y_lignepagebrh,
	 "Longueur" => $longeur_lignepagebrh,
	 "Diametre" => $diametre_lignepagebrh,
	 "Cubage" => $cubage_lignepagebrh,
	 "Annee" => $exercice
     );
  }

  echo json_encode($response);
  die;

?>