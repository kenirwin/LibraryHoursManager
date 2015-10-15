<?
include ('hours.class.php');
include ('admin.class.php');
$hours = new Hours;
$admin = new HoursAdmin;
if (isset($_REQUEST['action'])) {
    switch ($_REQUEST['action']) {
    case 'show-timeframe':
        $details = $hours->GetTimeframeDetails($_REQUEST['id']);
        print ($admin->EditTimeframeDetails($details,$hours,$_REQUEST['id']));
        break;
    case 'new-timeframe':
        print ($admin->EditTimeframeDetails());
        break;
    case 'show-preset':
        print_r ($_REQUEST);
        //      $details = $hours->GetTimeframeDetails($_REQUEST['id']);
        $details = $hours->GetSettingsDetails($_REQUEST['id']);
        print ($admin->EditPresetDetails($details, $_REQUEST['id']));
        print $details;
        break;
    }
}
?>