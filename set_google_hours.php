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
$special_start_date = AddDays($end_date,1);
$special_end_date = AddDays($start_date,120); // four months ahead for special date inclusion

$dates = $hours->ListDatesInRange($start_date,$end_date);
$special_dates = $hours->ListDatesInRange($special_start_date,$special_end_date);

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

foreach ($special_dates as $date) {
    $oneday = $hours->getHoursByDate($date,'google');
    if ($oneday['info_type'] != 'regular') {
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
    $before = print_r($location,true);
    print '<hr>';
} catch (Exception $e) {
    print ('Trouble connecting to Google: ' . $e->getMessage());
}

try { 
    /* do regular hours */
    $reg_hours = new Google_Service_MyBusiness_BusinessHours;
    $periods = array();
    foreach ($hours->Days() as $day) {
        $period = new Google_Service_MyBusiness_TimePeriod;
        $period->setOpenDay($reg[$day]['openday']);
        $period->setOpenTime($reg[$day]['opentime']);
        $period->setCloseDay($reg[$day]['closeday']);
        $period->setCloseTime($reg[$day]['closetime']);
        array_push($periods, $period);
    }
    $reg_hours->setPeriods($periods);
    $location->setRegularHours($reg_hours); 
    
    //    print_r($location);
    /* now update special hours */
    $special_hours = new Google_Service_MyBusiness_SpecialHours;
    $special_periods = array();
    foreach ($special_hours as $special_day) {
        //        print_r($special_day);
        $periodDay = new Google_Service_MyBusiness_Date;
        $periodDay->setYear(date('Y',strtotime($special_day['date']))); 
        $periodDay->setMonth(date('m',strtotime($special_day['date']))); 
        $periodDay->setDay(date('d',strtotime($special_day['date']))); 
        $period = new Google_Service_MyBusiness_SpecialHourPeriod;
        $period->setStartDate($periodDay);
        $period->setEndDate($periodDay);
        if (array_key_exists('is_closed',$special_day) && ($special_day['is_closed'])) {
            $period->setIsClosed(true);
        }
        else {
            //            $period->setOpenDay($special_day['openday']);
            $period->setOpenTime($special_day['opentime']);
            //$period->setCloseDay($special_day['closeday']);
            $period->setCloseTime($special_day['closetime']);
            $period->setIsClosed(false);
        }
        array_push($special_periods, $period);
    }

    $mybiz->accounts_locations->listAccountsLocations(G_MYBIZ_ACCOUNT)->locations[0]->setRegularHours($reg_hours);
    $mybiz->accounts_locations->listAccountsLocations(G_MYBIZ_ACCOUNT)->locations[0]->setSpecialHours($special_hours);

    $after = print_r($mybiz->accounts_locations->listAccountsLocations(G_MYBIZ_ACCOUNT)->locations[0],true);
} catch (Exception $e) {
    print 'Trouble updating Google model: '.$e->getMessage();
}

try {
    $opts = array();
    $opts = array('validateOnly'=>true);
    $response = $mybiz->accounts_locations->patch($location->name, $location, $opts);
    if (isset($response)) {
        print 'Update response from Google: ';
        print_r($response);
    }
} catch (Exception $e) {
    print $e->getMessage();
}


function AddDays($start_date, $days) {
    return date('Y-m-d', strtotime($start_date . ' + '.$days.' days'));
}
?>