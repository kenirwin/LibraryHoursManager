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
        print ($admin->EditTimeframeDetails($details,$hours,$_REQUEST['id']));
        break;
    case 'show-preset':
        if ($_REQUEST['id'] == 'new') {
            $details = $hours->GetSettingsDetails($_REQUEST['id']);
            print ($admin->ShowPresetDetails($details, $_REQUEST['id'],"edit"));
        }
        //      $details = $hours->GetTimeframeDetails($_REQUEST['id']);
        else {
            $details = $hours->GetSettingsDetails($_REQUEST['id']);
            print ($admin->ShowPresetDetails($details, $_REQUEST['id'],"show"));
        }
        break;

    case 'edit-preset':
        $details = $hours->GetSettingsDetails($_REQUEST['id']);
        print ($admin->ShowPresetDetails($details, $_REQUEST['id'],"edit"));
    }
}
?>