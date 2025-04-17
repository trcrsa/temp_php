<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: POST, GET, OPTIONS"); 
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With"); 

$servername = "turntable.proxy.rlwy.net";
$port = 13015;
$username = "root";
$password = "FFBCTxouWTQcPrWUMIkRPkFFwhWEnBWO";
$dbname = "railway";

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}
?>
