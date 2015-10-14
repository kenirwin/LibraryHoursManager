<?
include ('hours.class.php');
include ('admin.class.php');
$hours = new Hours;
$admin = new HoursAdmin;
if (isset($_REQUEST['action'])) {
    switch ($_REQUEST['action']) {
    case 'show-timeframe':
        $details = $hours->GetTimeframeDetails($_REQUEST['id']);
        print ($admin->EditTimeframeDetails($details));
        break;
    case 'new-timeframe':
        print ($admin->EditTimeframeDetails());
        break;
    }
}
?>