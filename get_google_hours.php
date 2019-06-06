<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
//header('Content-type: text/plain');

require_once './vendor/autoload.php';
require_once 'config.php';

use Google_Client;
use Google_Service_MyBusiness;

include ("hours.class.php");


/* Get hours from this app */

$hours = new Hours;
$start_date = date('Y-m-d');
$end_date = AddDays($start_date,7);

$dates = $hours->ListDatesInRange($start_date,$end_date);

$distinct_timeframes = $hours->CountTimeframesInSpan($start_date,$end_date);
if ($distinct_timeframes == 1) {
    //get regular hours
    if (isset($hours->preset_id)) {
        $reg = $hours->getCurrentRegularHours();
        //        print_r($reg);
    }
}

$special_hours = array();
foreach ($dates as $date) { 
    $oneday = $hours->getHoursByDate($date,'google');
    if ($distinct_timeframes != 1) { 
        // if multiple timeframes, add all to special hours
        array_push($special_hours,$oneday);
    }
    elseif ($oneday['info_type'] != 'regular') {
        //always push non-regular hours
        array_push($special_hours,$oneday);
    }    
}

/* Connect to Google */

try { 
    $client = new Google_Client;
    $client->setAuthConfig(G_CLIENT_SECRET_FILE);
    $client->refreshToken(G_REFRESH_TOKEN);
  
    $mybiz = new Google_Service_MyBusiness($client);
    $account = $mybiz->accounts->get(G_MYBIZ_ACCOUNT);
    $location = $mybiz->accounts_locations->listAccountsLocations(G_MYBIZ_ACCOUNT)->locations[0];
    var_dump($location);
} catch (Exception $e) {
    print ('Trouble connecting to Google: ' . $e->getMessage());
}


// NOW: just gotta actually use google client

function AddDays($start_date, $days) {
    return date('Y-m-d', strtotime($start_date . ' + '.$days.' days'));
}
?>