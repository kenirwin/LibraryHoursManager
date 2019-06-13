<?php
$username = "";
$database = "";
$hostname = "";
$password = "";
$charset = "utf8";

$local_path = ""; //http address of this directory

define ('HOSTNAME', $hostname);
define ('DATABASE', $database);
define ('USER', $username);
define ('PASS', $password);
define ('CHARSET', $charset);

define ('ERRORS_TO', ''); //an email address

define ('G_REFRESH_TOKEN',''); //get refresh token from Google 
define ('G_CLIENT_SECRET_FILE',''); //root-relative location of google client secret file
define ('G_MYBIZ_ACCOUNT',''); // google mybusiness account number, should be something like 'accounts/123456789'

?>