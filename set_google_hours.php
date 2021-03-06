<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once './vendor/autoload.php';
require_once 'config.php';

//use Google_Client;
//use Google_Service_MyBusiness;

include ("hours.class.php");
define ('G_SCOPE','https://www.googleapis.com/auth/plus.business.manage');

/* Settings */

$start_date = date('Y-m-d');
$end_date = AddDays($start_date,7); // only get regular hours for the coming week
$special_start_date = AddDays($end_date,1);
$special_end_date = AddDays($start_date,120); // get irregular hours for next 4 months


/* Get hours from this app */
$hours = new Hours;
$dates = $hours->ListDatesInRange($start_date,$end_date);
$special_dates = $hours->ListDatesInRange($special_start_date,$special_end_date);
$distinct_timeframes = $hours->CountTimeframesInSpan($start_date,$end_date);

/* 
   If only one timeframe this week, get the regular hours 
   Otherwise, just show inidividual hours for each day as "special hours"
*/

if ($distinct_timeframes == 1) {
    //get regular hours
    if (isset($hours->preset_id)) {
        $reg = $hours->getCurrentRegularHours();
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
} catch (Exception $e) {
    print ('Trouble connecting to Google: ' . $e->getMessage());
}

try { 
    /* do regular hours */
    $reg_hours = new Google_Service_MyBusiness_BusinessHours;
    $periods = array();
    //    foreach ($hours->Days() as $day) {
    if (is_array($reg)) {
        foreach ($reg as $day) {
            $period = new Google_Service_MyBusiness_TimePeriod;
            $period->setOpenDay($day['openday']);
            $period->setOpenTime($day['opentime']);
            $period->setCloseDay($day['closeday']);
            $period->setCloseTime($day['closetime']);
            array_push($periods, $period);
        }
        $reg_hours->setPeriods($periods);
        $location->setRegularHours($reg_hours); 
    }
    
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
    HandleException('Trouble updating Google model: '.$e->getMessage(), __LINE__);
}

try {
    $opts = array();
    //    $opts = array('validateOnly'=>true);
    $response = $mybiz->accounts_locations->patch($location->name, $location, $opts);
    if (isset($response)) {
        print 'Update response from Google: ';
        print_r($response);
    }
} catch (Exception $e) {
    HandleException('Failed to Update Google Hours:'. $e->getMessage(), __LINE__);
}

function HandleException($message, $line = null) {
    $body = 'Error updating Google Business Hours:'.PHP_EOL;
    $body.= __FILE__ . ' , line: ' . $line . PHP_EOL . PHP_EOL;
    $body.= $message;
    mail (ERRORS_TO, 'Error updating Google Business Hours', $body);
}

function AddDays($start_date, $days) {
    return date('Y-m-d', strtotime($start_date . ' + '.$days.' days'));
}
?>