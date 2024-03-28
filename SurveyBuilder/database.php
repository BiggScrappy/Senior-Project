<?php
$host = "damproject.cp0sgqaywkci.us-east-2.rds.amazonaws.com";
$dbname = "dam_database";
$username = "admin";
$password = "adminPass";


$mysqli = new mysqli($host,$username,$password,$dbname);

if ($mysqli->connect_errno){
    die("womp womp:" . $mysqli->connect_error);
}

return $mysqli;
// <!--database connection -->
// <!--Ember Adkins 901893134-->