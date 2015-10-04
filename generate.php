<?
include ("config.php");

if (isset($_REQUEST['format'])) {
    $format = $_REQUEST['format'];
}
else { $format = "text"; }

if ($format == "xmlIthaca") {
    header("Content-type: text/plain");
    print '<?xml version="1.0"?>'.PHP_EOL;
    print '<hours xsi:noNamespaceSchemaLocation="libraryHours.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'.PHP_EOL;
}

switch ($_REQUEST['action']) {
case "getlist":
    print (ListDailyHours($format));
    break;
case "oneday":
    if ($_REQUEST['date']) { 
        $date = date("Y-m-d",strtotime($_REQUEST['date']));
    }
    else { $date = date("Y-m-d");
    print (GetHoursByDate($date,$format));
    }
}

if ($format = "xmlIthaca") {
    print '</hours>'.PHP_EOL;
}

function ListDailyHours ($format) {
    $start = date("Y-m-d");
    $end = GetLastDate();
    $date = $start; 
    $output = "";
    while ($date <= $end) {
        if ($format == "xmlIthaca") {
            $output .=  GetHoursByDate($date, $format);
        }
        else {
            $output .= $date . ": " . GetHoursByDate($date) .'<br>'. PHP_EOL;
        }
        $date = date("Y-m-d", strtotime ($date . " + 1 day"));
    }
    return $output;
}

function GetHoursByDate ($date, $format="text") {
    $q = "SELECT * FROM exceptions WHERE `date` = '$date'";
    $r = mysql_query($q);
    if (mysql_num_rows($r) == 1) {
        $myrow = mysql_fetch_assoc($r);
        extract($myrow);
        if ($closed == "Y") {
            $hours = "CLOSED";
        }
        else {
            $hours = "$opentime - $closetime";
        }
    }
    else {
        $hours = GetHoursFromPreset($date);
    }
    if ($format == "text") { 
        return $hours;
    }
    elseif ($format == "xmlIthaca") {
        return '<day date="'.$date.'">'.$hours.'</day>'.PHP_EOL;
    }
}

function GetHoursFromPreset($date) {
    $day_of_week = date("l", strtotime($date));
    $q = "SELECT settings.* FROM settings,timeframes WHERE timeframes.first_date <= '$date' and timeframes.last_date >= '$date' and apply_preset_id = preset_id and day = '$day_of_week'";
    $r = mysql_query($q);
    if (mysql_num_rows($r) == 1) {
        $myrow=mysql_fetch_assoc($r);
        extract ($myrow);
        if ($closed == "Y") {
            return "CLOSED";
        }
        else {
            return "$opentime - $closetime";
        }
    }
    else { 
        return ($q);
    }
}

function GetLastDate () {
    $q = "SELECT last_date FROM timeframes ORDER BY last_date DESC LIMIT 0,1";
    $r = mysql_query($q);
    $myrow = mysql_fetch_row($r);
    $date = $myrow[0];
    return $date;
}
?>