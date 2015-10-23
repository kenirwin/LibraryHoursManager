<?
include ("hours.class.php");

if (is_array($argv)) { Argv2Request($argv); }
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
    print ($libHours->ListDailyHours($format,$_REQUEST));
    break;
case "oneday":
    if ($_REQUEST['date']) { 
        $date = date("Y-m-d",strtotime($_REQUEST['date']));
    }
    else { $date = date("Y-m-d"); }
    print ($libHours->GetHoursByDate($date,$format));
    break;
default: 
    include ("gen_form.php");
    break;
}

if ($format = "xmlIthaca") {
    print '</hours>'.PHP_EOL;
}


function Argv2Request($argv) {
    /*
      When $_REQUEST is empty and $argv is defined, 
      interpret $argv[1]...$argv[n] as key => value pairs
      and load them into the $_REQUEST array

      This allows the php command line to subsitute for GET/POST values, e.g.
      php script.php animal=fish color=red number=1 has_car=true has_star=false
     */


    if ($argv !== NULL && sizeof($_REQUEST) == 0) {
        $argv0 = array_shift($argv); // first arg is different and is not needed
        
        foreach ($argv as $pair) {
            list ($k, $v) = split("=", $pair);
            $_REQUEST[$k] = $v;
        }
    }
}
?>