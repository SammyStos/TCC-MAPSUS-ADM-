
<?php 

$sName = "162.241.203.11";
$uName = "mapsus45_sammyStos";
$pass = "mikaela@20";
$db_name = "mapsus45_db_adm";

try {
    $conn = new PDO("mysql:host=$sName;dbname=$db_name", 
                    $uName, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
  echo "Connection failed : ". $e->getMessage();
}
?>