<?php

$host = "localhost";
$user = "postgres";
$password = "020780";
$dbname = "dp";
$port = "5432";
$con = pg_connect("host=$host dbname=$dbname port=$port user=$user password=$password");

if(!$con){
    die("Connection failed.");
}

?>