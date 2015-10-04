<?
class Hours {

public function ListDailyHours ($format) {
    $start = date("Y-m-d");
    $end = $this->GetLastDate();
    $date = $start; 
    $output = "";
    while ($date <= $end) {
        if ($format == "xmlIthaca") {
            $output .=  $this->GetHoursByDate($date, $format);
        }
        else {
            $output .= $date . ": " . $this->GetHoursByDate($date) .'<br>'. PHP_EOL;
        }
        $date = date("Y-m-d", strtotime ($date . " + 1 day"));
    }
    return $output;
}

public function GetHoursByDate ($date, $format="text") {
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
        $hours = $this->GetHoursFromPreset($date);
    }
    if ($format == "text") { 
        return $hours;
    }
    elseif ($format == "xmlIthaca") {
        return '<day date="'.$date.'">'.$hours.'</day>'.PHP_EOL;
    }
}

public function GetHoursFromPreset($date) {
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

public function GetLastDate () {
    $q = "SELECT last_date FROM timeframes ORDER BY last_date DESC LIMIT 0,1";
    $r = mysql_query($q);
    $myrow = mysql_fetch_row($r);
    $date = $myrow[0];
    return $date;
}
} 
?>