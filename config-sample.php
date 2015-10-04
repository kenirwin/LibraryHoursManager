<?
$username = "";
$database = "";
$hostname = "";
$password = "";

$db = mysql_pconnect($hostname,$username,$password) or die ("cannot connect to MySQL");
mysql_select_db($database,$db) or die ("cannot select database");
?>