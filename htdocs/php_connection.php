<?php
$servername = "sql211.infinityfree.com";
$dbusername = "";
$password = ""; 
$db1name = "if0_37695577_soundsgood";

$connectiontosqldb = new mysqli($servername, $dbusername, $password, $db1name);
//Connection for band and rating data
if($sqldbon = 1){
mysqli_set_charset($connectiontosqldb, 'utf8mb4');
}

//Connection for user data is open
$connectiontosqldbusrs = new mysqli($servername, $dbusername, $password, $db1name);
mysqli_set_charset($connectiontosqldbusrs, 'utf8mb4');

//AWS API Gateway
$url_api_bands = "https://"; 
$url_api_bandratings = "https://"; 
?>
 