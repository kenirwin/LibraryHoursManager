<?php
include 'config.php';
if (file_exists("./local_headers_remote.php")) {
    //    include ("local_headers_remote.php");
}
?>
      <link href="<?=$local_path;?>style.css" rel="stylesheet">
      <link href="<?=$local_path;?>local_styles.css" rel="stylesheet">
      <?php

      $day_of_week = array('Sun','Mon','Tues','Wed','Thurs','Fri','Sat');

// Make sure the user input is numeric and not nasty
if (isset($_GET["prm"]) && is_numeric($_GET["prm"]) && isset($_GET["chm"]) && is_numeric($_GET["chm"])) {
    $prm = $_GET["prm"];
    $chm = $_GET["chm"];
}

$d= date("d");     // Finds today's date
$y= date("Y");     // Finds today's year

if (isset($prm) and $prm > 0) {
    $m = $prm + $chm;
} else {
    $m = date("m");
}

$no_of_days = date('t',mktime(0,0,0,$m,1,$y)); // This is to calculate number of days in a month

$mn=date('F',mktime(0,0,0,$m,1,$y)); // Month is calculated to display at the top of the calendar
$mql = date('m',mktime(0,0,0,$m,1,$y));
$yn=date('Y',mktime(0,0,0,$m,1,$y)); // Year is calculated to display at the top of the calendar
$j= date('w',mktime(0,0,0,$m,1,$y)); // This will calculate the week day of the first day of the month

$adj = "";
for($k=1; $k<=$j; $k++){ // Adjustment of date starting
    $adj .="<td class=\"hours_box empty\"></td>";
}


$calendar_file = "hours.xml";

$x_cal = simplexml_load_file($calendar_file);

$x_month_array = array();

foreach ($x_cal->day as $x_day) {
    $x_date = $x_day['date'];
    list($date_year, $date_month, $date_day) = explode("-", $x_date);
    $date_num = ltrim($date_day, '0');
    $date_month = ltrim($date_month, '0');
    if ($date_year==$yn && ($date_month==$m || $date_month==$m-12)) {
        $x_month_array[$date_num] = $x_day;
    }
}

//////////////////////
// Here we build the calendar . . .
//////////////////////

//$thisfile = basename($_SERVER['PHP_SELF']);
$calendar = "
<div id=\"calendar-wrapper\">
<div align=\"center\">
<h2 class=\"hours_nav\"><a href=\"" . $thisfile . "?prm=$m&chm=-1\" class=\"nav_arrow\">&#171;</a> <span class=\"month_header\">$mn $yn</span><a href=\"" . $thisfile . "?prm=$m&chm=1\" class=\"nav_arrow\">&#187;</a></h2>
</div>
<br />
<div align=\"center\">
<table id=\"hours\" class=\"hours_display\"><thead>
<tr class=\"hours_subheader\">
<th size=\"14%\">Sun</th><th size=\"14%\">Mon</th><th size=\"14%\">Tues</th><th size=\"14%\">Wed</th><th size=\"14%\">Thurs</th><th size=\"14%\">Fri</th><th size=\"14%\">Sat</th></tr></thead><tbody><tr>";

////// End of the top line showing name of the days of the week//////////

//////// Starting of the days//////////
for ($i=1;$i<=$no_of_days;$i++) {

    $dow = strftime("%a", strtotime($x_month_array[$i]['date']));
    $calendar.= $adj . "<td valign=\"top\" class=\"hours_box";

    // Add the coloured box for today
    if ($i == $d && $m == date("m")) {
        $calendar .= " hours_today";
    }
    if (strtolower($x_month_array[$i][0]) == "closed") {
        $calendar .= " closed";
    }

      $calendar.= "\"><span class=\"day_of_week\">" . $day_of_week[$j] . "</span>" . "<div class=\"hours_date\">$i</div>"; //This will display the date inside the calendar cell

  if (isset($x_month_array[$i])) { // there is an entry for this date in the XML file

    if ($x_month_array[$i] == "") { // and that entry isn't empty
      switch ($dow) {
        case "Sun":
          $calendar .= "Open at 10am";
          break;
        case "Fri":
          $calendar .= "Open until 10pm";
          break;
        case "Sat":
          $calendar .= "10am - 10pm";
          break;
        default:
          $calendar .= "Open 24 hours";
      }
    } else {
      $calendar .= $x_month_array[$i];
    }

  } else {
    $calendar .= " - ";
  }


  $calendar.= "</td>";

  $adj="";
  $j ++;
  if ($j==7) {
    $calendar.= "</tr><tr>";
    $j=0;
  }

}

$calendar.= "</tr></tbody></table></div>";

echo $calendar;
print "</div>"; //id=calendar-wrapper
?>

<? 
if (file_exists("./local_footers.php")) {
    //    include ("./local_footers.php"); 
}
?>
