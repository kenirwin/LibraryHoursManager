<?
$username = "";
$database = "";
$hostname = "";
$password = "";
$charset = "utf8";

$db = new PDO("mysql:host=$host;dbname=$db;charset=$charset", "$user", "$pass");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>