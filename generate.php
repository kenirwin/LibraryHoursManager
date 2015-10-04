<?
include ("config.php");
include ("hours.class.php");

if (isset($_REQUEST['format'])) {
    $format = $_REQUEST['format'];
}
else { $format = "text"; }

if ($format == "xmlIthaca") {
    header("Content-type: text/plain");
    print '<?xml version="1.0"?>'.PHP_EOL;
    print '<hours xsi:noNamespaceSchemaLocation="libraryHours.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'.PHP_EOL;
}

$libHours = new Hours;

switch ($_REQUEST['action']) {
case "getlist":
    print ($libHours->ListDailyHours($format));
    break;
case "oneday":
    if ($_REQUEST['date']) { 
        $date = date("Y-m-d",strtotime($_REQUEST['date']));
    }
    else { $date = date("Y-m-d"); }
    print ($libHours->GetHoursByDate($date,$format));
    break;
}

if ($format = "xmlIthaca") {
    print '</hours>'.PHP_EOL;
}
?>